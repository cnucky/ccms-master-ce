<?php

namespace App;

use App\ChargeHistory\ComputeNodeChargeHistory;
use App\Constants\StatusCode;
use App\Exceptions\InsufficientResourceException;
use App\IPPool\IPv4;
use App\IPPool\IPv6;
use App\Node\ComputeNode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Zone extends Model
{
    protected $guarded = [
        "id",
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function computeNodes()
    {
        return $this->hasMany(ComputeNode::class);
    }

    public function ipv4Pools()
    {
        return $this->belongsToMany(IPv4::class, "zone_has_ipv4_pools", "zone_id", "pool_id");
    }

    public function ipv6Pools()
    {
        return $this->belongsToMany(IPv6::class, "zone_has_ipv6_pools", "zone_id", "pool_id");
    }

    public function trafficShareGroup()
    {
        return $this->belongsTo(TrafficShareGroup::class);
    }

    public function packages()
    {
        return $this->belongsToMany(ComputeInstancePackage::class, "zone_has_compute_instance_packages", "zone_id", "package_id")->withPivot("id", "stock");
    }

    public function packageStocks()
    {
        return $this->hasMany(ZoneHasComputeInstancePackage::class, "zone_id");
    }

    public function chargeHistories()
    {
        return $this->hasManyThrough(ComputeNodeChargeHistory::class, ComputeNode::class, "zone_id", "compute_node_id");
    }

    public function getResourceCountersAttribute()
    {
        return DB::table("compute_nodes")
            ->selectRaw("IFNULL(SUM(GREATEST(total_memory_capacity_in_mib_unit - IFNULL(reserved_memory_capacity_in_mib_unit, 2048), 0)), 0) AS zone_total_memory_capacity, IFNULL(SUM(GREATEST(total_disk_capacity_in_gib_unit - IFNULL(reserved_disk_capacity_in_gib_unit, 4), 0)), 0) AS zone_total_disk_capacity, IFNULL(SUM(total_allocated_memory_in_mib_unit), 0) AS zone_total_allocated_memory_capacity, IFNULL(SUM(total_allocated_disk_in_gib_unit), 0) AS zone_total_allocated_disk_capacity")
            ->where("zone_id", $this->id)
            ->get()
            ->first()
        ;
    }

    public function assignNode(int $memory, int $diskCapacity, callable $callback)
    {
        DB::transaction(function () use ($memory, $diskCapacity, $callback) {
            if (!DB::update(<<<EOF
UPDATE compute_nodes
SET total_allocated_memory_in_mib_unit = total_allocated_memory_in_mib_unit + ?, 
    total_allocated_disk_in_gib_unit = total_allocated_disk_in_gib_unit + ?,
    instance_counter = instance_counter + 1,
    local_volume_counter = local_volume_counter + 1
WHERE total_memory_capacity_in_mib_unit - total_allocated_memory_in_mib_unit - IFNULL(reserved_memory_capacity_in_mib_unit, ?) > ?
  AND total_disk_capacity_in_gib_unit - total_allocated_disk_in_gib_unit - IFNULL(reserved_disk_capacity_in_gib_unit, ?) > ?
  AND current_memory_free_in_mib_unit > ?
  AND current_disk_free_in_gib_unit > ?
  AND status = ?
  AND zone_id = ?
  AND last_communicated_at > ?
  AND (
    max_instance IS NULL
    OR
    instance_counter < max_instance
  )
  AND LAST_INSERT_ID(id)
LIMIT 1
EOF
, [$memory, $diskCapacity, \App\Constants\ComputeNode::reservedMemoryCapacity(), $memory, \App\Constants\ComputeNode::reservedDiskCapacity(), $diskCapacity, $memory, $diskCapacity, StatusCode::STATUS_NORMAL, $this->id, date("Y-m-d H:i:s", time()- 600)]))
                throw new InsufficientResourceException();
            $computeNodeId = DB::getPdo()->lastInsertId();
            call_user_func($callback, $computeNodeId);
        });
    }

    public function hasPackage($packageId)
    {
        return $this->packages()->where("package_id", $packageId)->first();
    }
}
