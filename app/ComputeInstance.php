<?php

namespace App;

use App\ChargeHistory\ComputeInstancePackageChargeHistory;
use App\ChargeHistory\ComputeNodeChargeHistory;
use App\ComputeInstance\BandwidthUsage;
use App\ComputeInstance\CPUUsage;
use App\ComputeInstance\Device\CDROM;
use App\ComputeInstance\Device\Floppy;
use App\ComputeInstance\Device\NetworkInterface;
use App\ComputeInstance\DiskIOUsage;
use App\ComputeInstance\LocalVolume;
use App\ComputeInstance\OperationRequest;
use App\ComputeInstance\TrafficUsage;
use App\Constants\ComputeInstance\OperationRequest\StatusCode;
use App\Constants\ComputeInstanceStatusCode;
use App\Constants\ComputeResourceOperation\ResourceTypeCode;
use App\Constants\CreditRecordType;
use App\IPPool\IPv4Assignment;
use App\IPPool\IPv6Assignment;
use App\Node\ComputeNode;
use App\Utils\Charging\ChargeableModel;
use App\Utils\Common;
use App\Utils\ComputeInstance\ComputeInstanceUtils;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use YunInternet\CCMSCommon\Constants\NetworkType;

class ComputeInstance extends Model
{
    use SoftDeletes;

    use ChargeableModel;

    protected $dates = ['deleted_at'];

    protected $guarded = [];

    protected $appends = [
        "instance_size",
        "client_instance_size",
        /*
        "zone",
        "processing_operation_requests"
        */
    ];

    protected $hidden = [
        "password",
        "vnc_password",
        "override_vCPU",
        "override_memory",
        "override_public_ipv4",
        "override_public_ipv4_block_size",
        "override_public_ipv6",
        "override_public_ipv6_block_size",
        "override_traffic",
        "override_inbound_traffic",
        "override_outbound_traffic",
        "override_inbound_bandwidth",
        "override_outbound_bandwidth",
        "override_io_weight",
        "override_read_bytes_sec",
        "override_write_bytes_sec",
        "override_read_iops_sec",
        "override_write_iops_sec",
        "override_price_per_hour",
    ];

    public function node()
    {
        return $this->belongsTo(ComputeNode::class, "compute_node_id");
    }

    public function package()
    {
        return $this->belongsTo(ComputeInstancePackage::class, "compute_instance_package_id");
    }

    public function attachedLocalVolumes()
    {
        return $this->hasMany(LocalVolume::class, "attached_compute_instance_id");
    }

    public function networkInterfaces()
    {
        return $this->hasMany(NetworkInterface::class);
    }

    public function cdroms()
    {
        return $this->hasMany(CDROM::class);
    }

    public function floppies()
    {
        return $this->hasMany(Floppy::class);
    }

    public function ipv4s()
    {
        return $this->hasManyThrough(IPv4Assignment::class, NetworkInterface::class, "compute_instance_id", "nic_id", "id", "id");
    }


    public function ipv6s()
    {
        return $this->hasManyThrough(IPv6Assignment::class, NetworkInterface::class, "compute_instance_id", "nic_id", "id", "id");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function operationRequests()
    {
        return $this
            ->hasMany(OperationRequest::class, "resource_id")
            ->where("resource_type", ResourceTypeCode::TYPE_COMPUTE_INSTANCE)
            ;
    }

    public function processingOperationRequests()
    {
        return $this->operationRequests()->where("is_processed", 0);
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function cpuUsages()
    {
        return $this->hasMany(CPUUsage::class);
    }

    public function trafficUsages()
    {
        return $this->hasManyThrough(TrafficUsage::class, NetworkInterface::class, "compute_instance_id", "network_interface_id");
    }

    public function bandwidthUsages()
    {
        return $this->hasManyThrough(BandwidthUsage::class, NetworkInterface::class, "compute_instance_id", "network_interface_id");
    }

    public function diskIOUsages()
    {
        return $this->hasMany(DiskIOUsage::class);
    }

    /**
     * @return ComputeInstanceUtils
     */
    public function createUtils()
    {
        return ComputeInstanceUtils::fromComputeInstanceModel($this);
    }

    public function calculateRecentAverageCPUUtilization($minTime)
    {
        return DB::table("compute_instance_cpu_usages")
            ->selectRaw("IFNULL(AVG(cpu_usage), 0) AS average_cpu_usage")
            ->where("compute_instance_id", $this->id)
            ->where("microtime", ">=", $minTime)
            ->first()
            ->average_cpu_usage
        ;
    }

    public function calculateRecentAverageDiskIOUsage($minTime)
    {
        return DB::table("compute_instance_disk_io_usages")
            ->selectRaw("IFNULL(AVG(rd_req_per_second), 0) AS average_rd_req_per_second, IFNULL(AVG(rd_bytes_per_second), 0) AS average_rd_bytes_per_second, IFNULL(AVG(wr_req_per_second), 0) AS average_wr_req_per_second, IFNULL(AVG(wr_bytes_per_second), 0) AS average_wr_bytes_per_second")
            ->where("compute_instance_id", $this->id)
            ->where("microtime", ">=", $minTime)
            ->first()
        ;
    }

    public function calculateRecentAverageBandwidthUsage($minTime)
    {
        $networkInterfaceCollection = $this->networkInterfaces()->get();
        $networkInterfaceIdList = $networkInterfaceCollection->pluck("id")->toArray();

        $result = DB::table("compute_instance_bandwidth_usages")
            ->selectRaw(<<<EOF
network_interface_id,
IFNULL(AVG(rx_bytes_per_second), 0) AS average_rx_bytes_per_second,
IFNULL(AVG(rx_packets_per_second), 0) AS average_rx_packets_per_second,
IFNULL(AVG(tx_bytes_per_second), 0) AS average_tx_bytes_per_second,
IFNULL(AVG(tx_packets_per_second), 0) AS average_tx_packets_per_second
EOF
)
            ->whereIn("network_interface_id", $networkInterfaceIdList)
            ->where("microtime", ">=", $minTime)
            ->groupBy("network_interface_id")
            ->get()
        ;

        $networkInterfaceTypeMap = $networkInterfaceCollection->pluck("type", "id")->toArray();

        $averages = [];
        foreach ($result as $row) {
            $networkInterfaceId = $row->network_interface_id;
            if (!array_key_exists($networkInterfaceId, $networkInterfaceTypeMap)) {
                continue;
            }
            $type = $networkInterfaceTypeMap[$networkInterfaceId];
            $averages[$type] = $row;
        }

        return $averages;
    }

    public function instanceSize()
    {
        /**
         * @var ComputeInstancePackage $package
         */
        $package = $this->package;
        $directSize = $this->directSize();
        if (is_null($package)) {
            return $directSize;
        }

        $instanceSize = [];

        foreach ($package->packageSize() as $item => $value){
            if (is_null($directSize[$item])) {
                $instanceSize[$item] = $value;
            } else {
                $instanceSize[$item] = $directSize[$item];
            }
        }

        return $instanceSize;
    }

    public function getFreeTXTrafficValue()
    {
        $overrideTXTraffic = $this->override_outbound_traffic;
        if (!is_null($overrideTXTraffic))
            return $overrideTXTraffic;

        if (!$this->package) {
            return null;
        }

        return $this->package->outbound_traffic;
    }

    public function getInstanceSizeAttribute()
    {
        return $this->instanceSize();
    }

    public function getClientInstanceSizeAttribute()
    {
        $instanceSize = $this->instanceSize();

        return [
            "vCPU" => $instanceSize["vCPU"],
            "memory" => $instanceSize["memory"],
            "min_storage_capacity" => $instanceSize["min_storage_capacity"],
            "max_storage_capacity" => $instanceSize["max_storage_capacity"],
            "public_ipv4" => $instanceSize["public_ipv4"],
            "public_ipv4_block_size" => $instanceSize["public_ipv4_block_size"],
            "max_elastic_ipv4_block" => $instanceSize["max_elastic_ipv4_block"],
            "public_ipv6" => $instanceSize["public_ipv6"],
            "public_ipv6_block_size" => $instanceSize["public_ipv6_block_size"],
            "max_elastic_ipv6_block" => $instanceSize["max_elastic_ipv6_block"],
            "traffic" => $instanceSize["traffic"],
            "inbound_traffic" => $instanceSize["inbound_traffic"],
            "outbound_traffic" => $instanceSize["outbound_traffic"],
            "inbound_bandwidth" => $instanceSize["inbound_bandwidth"],
            "outbound_bandwidth" => $instanceSize["outbound_bandwidth"],
            "price_per_hour" => $instanceSize["price_per_hour"],
        ];
    }

    public function getHourlyPriceAttribute()
    {
        $directHourlyPrice = $this->override_price_per_hour;
        if (is_null($directHourlyPrice))
            return $this->package->price_per_hour;
        return $directHourlyPrice;
    }

    public function getZoneAttribute()
    {
        /**
         * @var ComputeNode $node
         */
        $node = $this->node;

        return $node->zone()->with("region")->first();
    }

    public function getProcessingOperationRequestsAttribute()
    {
        return $this->operationRequests()->whereNotIn("operation_status", [StatusCode::STATUS_SUCCESS, StatusCode::STATUS_FAILED])->get();
    }

    public function directSize()
    {
        return [
            "vCPU" => $this->override_vCPU,
            "memory" => $this->override_memory,
            "min_storage_capacity" => $this->override_min_storage_capacity,
            "max_storage_capacity" => $this->override_max_storage_capacity,
            "public_ipv4" => $this->override_public_ipv4,
            "public_ipv4_block_size" => $this->override_public_ipv4_block_size,
            "max_elastic_ipv4_block" => $this->override_max_elastic_ipv4_block,
            "public_ipv6" => $this->override_public_ipv6,
            "public_ipv6_block_size" => $this->override_public_ipv6_block_size,
            "max_elastic_ipv6_block" => $this->override_max_elastic_ipv6_block,
            "traffic" => $this->override_traffic,
            "inbound_traffic" => $this->override_inbound_traffic,
            "outbound_traffic" => $this->override_outbound_traffic,
            "inbound_bandwidth" => $this->override_inbound_bandwidth,
            "outbound_bandwidth" => $this->override_outbound_bandwidth,
            "io_weight" => $this->override_io_weight,
            "read_bytes_sec" => $this->override_read_bytes_sec,
            "write_bytes_sec" => $this->override_write_bytes_sec,
            "read_iops_sec" => $this->override_read_iops_sec,
            "write_iops_sec" => $this->override_write_iops_sec,
            "price_per_hour" => $this->override_price_per_hour,
        ];
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getHourlyUnitPrice(): string
    {
        return $this->getHourlyPriceAttribute();
    }

    public function getCreditRecordType(): int
    {
        return CreditRecordType::TYPE_CHARGE_COMPUTE_INSTANCE;
    }

    public function getCreditRecordDescription($lastChargeTime, $currentChargingTime, $chargeTimeInSecond)
    {
        return sprintf("%s: %s - %s (%s seconds)", $this->unique_id, $lastChargeTime, $currentChargingTime, $chargeTimeInSecond);
    }

    protected function createRelativeChargeHistory($amount, $time)
    {
        ComputeInstancePackageChargeHistory::query()->create([
            "compute_instance_package_id" => $this->compute_instance_package_id,
            "amount" => $amount,
            "created_at" => $time,
        ]);
        ComputeNodeChargeHistory::query()->create([
            "compute_node_id" => $this->compute_node_id,
            "type" => CreditRecordType::TYPE_CHARGE_COMPUTE_INSTANCE,
            "amount" => $amount,
            "created_at" => $time,
        ]);
    }

    public function deleteWithNodeCounterUpdate()
    {
        DB::transaction(function () {
            $instanceSize = $this->instanceSize();
            $memory = $instanceSize["memory"];
            $compute_node_id = $this->compute_node_id;
            $zoneId = $this->node->zone->id;
            $packageId = $this->compute_instance_package_id;
            if ($this->delete()) {
                DB::update("UPDATE compute_nodes SET 
  total_allocated_memory_in_mib_unit = GREATEST(CAST(total_allocated_memory_in_mib_unit AS SIGNED) - ?, 0),
  instance_counter = GREATEST(CAST(instance_counter - 1 AS SIGNED), 0)
WHERE id = ?", [$memory, $compute_node_id]);

                try {
                    ZoneHasComputeInstancePackage::query()
                        ->where("zone_id", $zoneId)
                        ->where("package_id", $packageId)
                        ->whereNotNull("stock")
                        ->increment("stock");
                } catch (\Throwable $throwable) {
                    Common::logException($throwable);
                }
            }
        });
    }

    /*
    public function chargeRelativeResources($beforeCharge = null, $afterCharged = null)
    {
        try {
            try {
                $networkInterface = $this->networkInterfaces()->where("type", NetworkType::TYPE_PUBLIC_NETWORK)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return;
            }

            TrafficUsage::prepareChargeableItems($networkInterface->id);
            $totalTrafficInByteUnit = TrafficUsage::sumChargingTXByte($networkInterface->id);

        } catch (\Throwable $throwable) {
            Common::logException($throwable);
        }
    }
    */

    public static function calcInstanceRecent20MinutesUsage($computeNodeId)
    {
        $minTime = time() - 1200;
        /**
         * @var ComputeInstance $computeInstance
         */
        foreach (self::query()->where("compute_node_id", $computeNodeId)->get() as $computeInstance) {
            $cpuUtilization = $computeInstance->calculateRecentAverageCPUUtilization($minTime);
            $diskIOUsage = $computeInstance->calculateRecentAverageDiskIOUsage($minTime);
            $bandwidthUsage = $computeInstance->calculateRecentAverageBandwidthUsage($minTime);

            $averageRDReqPerSecond = $diskIOUsage->average_rd_req_per_second;
            $averageRDBytesPerSecond = $diskIOUsage->average_rd_bytes_per_second;
            $averageWRReqPerSecond = $diskIOUsage->average_wr_req_per_second;
            $averageWRBytesPerSecond = $diskIOUsage->average_wr_bytes_per_second;

            if (array_key_exists(NetworkType::TYPE_PUBLIC_NETWORK, $bandwidthUsage)) {
                $publicNetworkBandwidthUsage = $bandwidthUsage[NetworkType::TYPE_PUBLIC_NETWORK];
                $publicNetworkRXBytePerSecond = $publicNetworkBandwidthUsage->average_rx_bytes_per_second;
                $publicNetworkTXBytesPerSecond = $publicNetworkBandwidthUsage->average_tx_bytes_per_second;
            } else {
                $publicNetworkRXBytePerSecond = 0;
                $publicNetworkTXBytesPerSecond = 0;
            }

            if (array_key_exists(NetworkType::TYPE_PRIVATE_NETWORK, $bandwidthUsage)) {
                $privateNetworkBandwidthUsage = $bandwidthUsage[NetworkType::TYPE_PRIVATE_NETWORK];
                $privateNetworkRXBytePerSecond = $privateNetworkBandwidthUsage->average_rx_bytes_per_second;
                $privateNetworkTXBytesPerSecond = $privateNetworkBandwidthUsage->average_tx_bytes_per_second;
            } else {
                $privateNetworkRXBytePerSecond = 0;
                $privateNetworkTXBytesPerSecond = 0;
            }

            $computeInstance->update([
                "twenty_minutes_average_cpu_utilization" => $cpuUtilization,
                "twenty_minutes_average_public_network_rx_bytes_per_second" => $publicNetworkRXBytePerSecond,
                "twenty_minutes_average_public_network_tx_bytes_per_second" => $publicNetworkTXBytesPerSecond,
                "twenty_minutes_average_private_network_rx_bytes_per_second" => $privateNetworkRXBytePerSecond,
                "twenty_minutes_average_private_network_tx_bytes_per_second" => $privateNetworkTXBytesPerSecond,
                "twenty_minutes_average_disk_read_bytes_per_second" => $averageRDBytesPerSecond,
                "twenty_minutes_average_disk_write_bytes_per_second" => $averageWRBytesPerSecond,
                "twenty_minutes_average_disk_read_req_per_second" => $averageRDReqPerSecond,
                "twenty_minutes_average_disk_write_req_per_second" => $averageWRReqPerSecond,
            ]);
        }
    }

    public static function calculateTotalHourlyPrice()
    {
        return DB::select(<<<EOF
SELECT IFNULL(SUM(IFNULL(compute_instances.override_price_per_hour, IFNULL(compute_instance_packages.price_per_hour, 0))), 0) AS total_price_per_hour
FROM compute_instances
       LEFT JOIN compute_instance_packages
                 ON compute_instances.compute_instance_package_id = compute_instance_packages.id
WHERE compute_instances.deleted_at IS NULL
  AND compute_instances.status != ?
EOF
        , [ComputeInstanceStatusCode::STATUS_CREATE_UNSUCCESSFULLY])[0]->total_price_per_hour;

    }
}
