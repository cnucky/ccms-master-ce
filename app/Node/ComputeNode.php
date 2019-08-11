<?php

namespace App\Node;

use App\Certificate;
use App\ChargeHistory\ComputeNodeChargeHistory;
use App\ComputeInstance;
use App\ComputeNode\BandwidthUsage;
use App\ComputeNode\CPUUsage;
use App\ComputeNode\DiskIOUsage;
use App\ComputeNode\DiskSpaceUsage;
use App\ComputeNode\LoadAverage;
use App\ComputeNode\MemoryUsage;
use App\Constants\PKI;
use App\Constants\StatusCode;
use App\Exceptions\InsufficientResourceException;
use App\IPPool\IPv4;
use App\IPPool\IPv6;
use App\TrustedCertificate;
use App\Utils\Contract\PKIContract;
use App\Utils\Node\ComputeNode\ComputeNodeUtil;
use App\Utils\PKIBuilder;
use App\Zone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ComputeNode extends Model implements PKIContract
{
    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;

    protected $guarded = [];

    protected $hidden = [
        "password",
        "private_key",
        "certificate",
        "token",
    ];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function trustedCertificate()
    {
        return $this->belongsTo(TrustedCertificate::class);
    }

    public function cpuUsages()
    {
        return $this->hasMany(CPUUsage::class);
    }

    public function diskIOUsages()
    {
        return $this->hasMany(DiskIOUsage::class);
    }

    public function diskSpaceUsages()
    {
        return $this->hasMany(DiskSpaceUsage::class);
    }

    public function bandwidthUsages()
    {
        return $this->hasMany(BandwidthUsage::class);
    }

    public function loadAverages()
    {
        return $this->hasMany(LoadAverage::class);
    }

    public function memoryUsages()
    {
        return $this->hasMany(MemoryUsage::class);
    }

    public function instances()
    {
        return $this->hasMany(ComputeInstance::class);
    }

    public function localVolumes()
    {
        return $this->hasMany(ComputeInstance\LocalVolume::class);
    }

    public function serverCertificate()
    {
        return $this->belongsTo(Certificate::class, "certificate_id");
    }

    public function privateKey()
    {
        return Crypt::decryptString($this->private_key);
    }

    public function createUtil()
    {
        $PKIBuilder = new PKIBuilder(PKI::TYPE_COMPUTE_NODE, $this->host, $this);
        return new ComputeNodeUtil($this->host, $PKIBuilder);
    }

    public function ipv4Pools()
    {
        return $this->belongsToMany(IPv4::class, "node_has_ipv4_pools", "node_id", "pool_id");
    }

    public function ipv6Pools()
    {
        return $this->belongsToMany(IPv6::class, "node_has_ipv6_pools", "node_id", "pool_id");
    }

    public function chargeHistories()
    {
        return $this->hasMany(ComputeNodeChargeHistory::class);
    }

    public function calculateAllocatedMemory()
    {
        $computeNodeId = $this->id;
        DB::update(<<<EOF
UPDATE compute_nodes, (
  SELECT SUM(total_memory) AS total_memory
  FROM (
         (SELECT IFNULL(SUM(compute_instance_packages.memory), 0) as total_memory
          FROM compute_instances
                 INNER JOIN compute_instance_packages
                            ON compute_instances.compute_instance_package_id =
                               compute_instance_packages.id
          WHERE compute_instances.override_memory IS NULL
            AND compute_instances.deleted_at IS NULL
            AND compute_instances.compute_node_id = ?)
         UNION
         (SELECT IFNULL(SUM(compute_instances.override_memory), 0) as total_memory
          FROM compute_instances
          WHERE compute_instances.override_memory IS NOT NULL
            AND compute_instances.deleted_at IS NULL
            AND compute_instances.compute_node_id = ?)
       ) AS a
) AS b SET total_allocated_memory_in_mib_unit = b.total_memory WHERE compute_nodes.id = ?;
EOF
, [$computeNodeId, $computeNodeId, $computeNodeId]);
    }

    public function calculateAllocatedDiskCapacity()
    {
        $computeNodeId = $this->id;
        DB::update(<<<EOF
UPDATE compute_nodes, (SELECT IFNULL(SUM(capacity), 0) AS total_capacity
                       FROM compute_instance_local_volumes
                       WHERE compute_node_id = ?) AS a
SET compute_nodes.total_allocated_disk_in_gib_unit = a.total_capacity
WHERE compute_nodes.id = ?
EOF
, [$computeNodeId, $computeNodeId]);
    }

    public function allocateMemory(int $capacityRequirement, callable $callback)
    {
        DB::transaction(function () use ($capacityRequirement, $callback) {
            if (!DB::update(<<<EOF
UPDATE compute_nodes
SET total_allocated_memory_in_mib_unit = total_allocated_memory_in_mib_unit + ?
WHERE id = ?
  AND total_memory_capacity_in_mib_unit - total_allocated_memory_in_mib_unit - IFNULL(reserved_memory_capacity_in_mib_unit, ?) > ?
  AND current_memory_free_in_mib_unit > ?
  AND LAST_INSERT_ID(id)
EOF
                , [$capacityRequirement, $this->id, \App\Constants\ComputeNode::reservedMemoryCapacity(), $capacityRequirement, $capacityRequirement])) {
                throw new InsufficientResourceException();
            }
            call_user_func($callback, DB::getPdo()->lastInsertId(), $this);
        });
    }

    public function allocateDiskCapacity(int $capacityRequirement, callable $callback)
    {
        DB::transaction(function () use ($capacityRequirement, $callback) {
            if (!DB::update(<<<EOF
UPDATE compute_nodes
SET total_allocated_disk_in_gib_unit = total_allocated_disk_in_gib_unit + ?
WHERE id = ?
  AND total_disk_capacity_in_gib_unit - total_allocated_disk_in_gib_unit - IFNULL(reserved_disk_capacity_in_gib_unit, ?) > ?
  AND current_disk_free_in_gib_unit > ?
  AND LAST_INSERT_ID(id)
EOF
, [$capacityRequirement, $this->id, \App\Constants\ComputeNode::reservedDiskCapacity(), $capacityRequirement, $capacityRequirement])) {
                throw new InsufficientResourceException();
            }
            call_user_func($callback, DB::getPdo()->lastInsertId(), $this);
        });
    }

    public function releaseDiskCapacity(int $capacity, callable $callback)
    {
        DB::transaction(function () use ($capacity, $callback) {
            DB::update(<<<EOF
UPDATE compute_nodes SET total_allocated_disk_in_gib_unit = total_allocated_disk_in_gib_unit - ? WHERE id = ?
EOF
, [$callback, $this->id]);
            call_user_func($callback, $this->id, $this);
        });
    }

    public function refreshComputeInstanceCounter()
    {
        DB::update(<<<EOF
UPDATE compute_nodes
SET compute_nodes.instance_counter = (
  SELECT COUNT(*)
  FROM compute_instances
  WHERE compute_instances.compute_node_id = compute_nodes.id
  AND compute_instances.deleted_at IS NULL)
WHERE compute_nodes.id = ?
EOF
, [$this->id]);
    }

    public function refreshLocalVolumeCounter()
    {
        DB::update(<<<EOF
UPDATE compute_nodes
SET compute_nodes.local_volume_counter = (
  SELECT COUNT(*)
  FROM compute_instance_local_volumes
  WHERE compute_instance_local_volumes.compute_node_id = compute_nodes.id
)
WHERE compute_nodes.id = ?
EOF
, [$this->id]);
    }

    public function increaseComputeInstanceCounter()
    {
        $this->increment("instance_counter");
    }

    public function increaseLocalVolumeCounter()
    {
        $this->increment("local_volume_counter");
    }

    public function decreaseComputeInstanceCounter()
    {
        $this->decrement("instance_counter");
    }

    public function decreaseLocalVolumeCounter()
    {
        $this->decrement("local_volume_counter");
    }

    public function getVersion(): string
    {
        return $this->updated_at . $this->trustedCertificate->updated_at;
    }

    public function getCACertificate(): string
    {
        return $this->trustedCertificate->certificate;
    }

    public function getClientPrivateKey(): string
    {
        return $this->privateKey();
    }

    public function getClientCertificate(): string
    {
        return $this->certificate;
    }

    public static function whereOnline($builder)
    {
        return $builder->where("last_communicated_at", ">=", date("Y-m-d H:i:s", time() - 600));
    }

    public static function whereOnlineAndAvailable($builder)
    {
        return self::whereOnline($builder)->where("compute_nodes.status", StatusCode::STATUS_NORMAL);
    }
}
