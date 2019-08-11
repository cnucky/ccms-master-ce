<?php

namespace App\Http\Controllers\SlaveAPI;

use App\ComputeInstance;
use App\ComputeNode\BandwidthUsage;
use App\ComputeNode\CPUUsage;
use App\ComputeNode\DiskIOUsage;
use App\ComputeNode\DiskSpaceUsage;
use App\ComputeNode\LoadAverage;
use App\ComputeNode\MemoryUsage;
use App\ComputeNodePublicFloppy;
use App\ComputeNodePublicImage;
use App\ComputeNodePublicISO;
use App\Node\ComputeNode;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PingController extends Controller
{
    public function __invoke(Request $request, ComputeNode $computeNode)
    {
        $this->storePublicImages($request, $computeNode);
        $this->storePublicISOs($request, $computeNode);
        $this->storePublicFloppies($request, $computeNode);
        $this->storeInstanceStatus($request, $computeNode);

        $this->storeCPUStatus($request, $computeNode);
        $this->storeMemoryStatus($request, $computeNode);
        $this->storeDiskStatus($request, $computeNode);
        $this->storeLoadAverage($request, $computeNode);
        $this->storeNetworkStatus($request, $computeNode);

        $this->storeInstanceStatus($request, $computeNode);

        $computeNode->fill([
            "uptime" => $request->data["nodeStatus"]["uptime"],
            "last_communicated_at" => date("Y-m-d H:i:s"),
        ]);
        $computeNode->save();

        // CalcInstanceRecentUsage::dispatch($computeNode->id);
        ComputeInstance::calcInstanceRecent20MinutesUsage($computeNode->id);

        return [
            "result" => true,
            "type" => "pong",
            "data" => "pong",
        ];
    }

    private function storePublicImages(Request $request, ComputeNode $computeNode)
    {
        DB::transaction(function () use ($computeNode, $request) {
            ComputeNodePublicImage::query()->where("compute_node_id", $computeNode->id)->delete();
            $images = [];
            foreach ($request->data["images"]["public"] as $internalName => $versions) {
                foreach ($versions as $version) {
                    $images[] = [
                        "compute_node_id" => $computeNode->id,
                        "internal_name" => $internalName,
                        "version" => $version,
                    ];
                }
            }
            ComputeNodePublicImage::query()->insert($images);
        });
    }

    private function storePublicISOs(Request $request, ComputeNode $computeNode)
    {
        DB::transaction(function () use ($computeNode, $request) {
            ComputeNodePublicISO::query()->where("compute_node_id", $computeNode->id)->delete();
            $isos = [];
            foreach ($request->data["isos"]["public"] as $iso) {
                $isos[] = [
                    "compute_node_id" => $computeNode->id,
                    "internal_name" => $iso,
                ];
            }
            ComputeNodePublicISO::query()->insert($isos);
        });
    }

    private function storePublicFloppies(Request $request, ComputeNode $computeNode)
    {
        DB::transaction(function () use ($computeNode, $request) {
            ComputeNodePublicFloppy::query()->where("compute_node_id", $computeNode->id)->delete();
            $floppies = [];
            foreach ($request->data["floppies"]["public"] as $floppy) {
                $floppies[] = [
                    "compute_node_id" => $computeNode->id,
                    "internal_name" => $floppy,
                ];
            }
            ComputeNodePublicFloppy::query()->insert($floppies);
        });
    }

    private function storeInstanceStatus(Request $request, ComputeNode $computeNode)
    {
        foreach ($request->data["instances"] as $uniqueId => $status) {
            try {
                /**
                 * @var ComputeInstance $computeInstance
                 */
                $computeInstance = ComputeInstance::query()->where("unique_id", $uniqueId)->firstOrFail();
                $computeInstance->update([
                    "power_status" => $status["power"],
                ]);
                $this->storeComputeInstanceCPUUsages($status["status"]["cpu_usages"], $computeInstance);
                $this->storeComputeInstanceDiskIOUsages($status["status"]["disk_usages"], $computeInstance);
                $this->storeComputeInstanceNetworkUsages($status["status"]["network_interfaces"], $computeInstance);
            } catch (ModelNotFoundException $modelNotFoundException) {
            }
        }
    }

    private function storeCPUStatus(Request $request, ComputeNode $computeNode)
    {
        $nodeCPUStatus = $request->data["nodeStatus"]["cpu"];

        $computeNode->fill([
            "cpu_model" => $nodeCPUStatus["information"]["model"],
            "cpu_cores" => $nodeCPUStatus["information"]["processors"],
        ]);

        $computeNodeId = $computeNode->id;

        foreach ($nodeCPUStatus["usage"] as $cpuUsage) {
            $attribute = [
                "compute_node_id" => $computeNodeId,
                "id" => $cpuUsage["basic_cpu_statistics_id"],
                "processor" => $cpuUsage["processor"],
            ];
            $values = [
                "user" => $cpuUsage["user"],
                "nice" => $cpuUsage["nice"],
                "system" => $cpuUsage["system"],
                "idle" => $cpuUsage["idle"],
                "iowait" => $cpuUsage["iowait"],
                "irq" => $cpuUsage["irq"],
                "softirq" => $cpuUsage["softirq"],
                "steal" => $cpuUsage["steal"],
                "guest" => $cpuUsage["guest"],
                "guest_nice" => $cpuUsage["guest_nice"],
                "microtime" => $cpuUsage["basic_c_p_u_statistics"]["microtime"],
            ];
            CPUUsage::query()->updateOrInsert($attribute, $values);
        }

        if ($cpuUsage = $computeNode->cpuUsages()->orderByDesc("microtime")->limit(1)->first()) {
            $computeNode->fill([
                "cpu_utilization" => $cpuUsage->getUtilizationAttribute(),
            ]);
        }
    }

    private function storeComputeInstanceCPUUsages($cpuUsages, ComputeInstance $computeInstance)
    {
        $computeInstanceId = $computeInstance->id;

        foreach ($cpuUsages as $cpuUsage) {
            $attributes = [
                "compute_instance_id" => $computeInstanceId,
                "id" => $cpuUsage["basic_cpu_statistics_id"],
            ];
            $values = [
                "cpu_usage" => $cpuUsage["cpu_usage"],
                "user_usage" => $cpuUsage["user_usage"],
                "system_usage" => $cpuUsage["system_usage"],
                "microtime" => $cpuUsage["basic_c_p_u_statistics"]["microtime"]
            ];
            ComputeInstance\CPUUsage::query()->updateOrInsert($attributes, $values);
        }
    }

    private function storeMemoryStatus(Request $request, ComputeNode $computeNode)
    {
        $computeNodeId = $computeNode->id;

        $memoryStatus = $request->data["nodeStatus"]["memory"];
        foreach ($memoryStatus["usage"] as $usage) {
            $attributes = [
                "compute_node_id" => $computeNodeId,
                "id" => $usage["id"],
            ];
            $values = [
                "total" => $usage["total"],
                "free" => $usage["free"],
                "available" => $usage["available"],
                "microtime" => $usage["microtime"],
            ];
            MemoryUsage::query()->updateOrInsert($attributes, $values);
        }

        if ($latestMemoryUsage = MemoryUsage::query()->where("compute_node_id", $computeNodeId)->orderByDesc("microtime")->limit(1)->first()) {
            $computeNode->fill([
                "total_memory_capacity_in_mib_unit" => intdiv($latestMemoryUsage->total, 1024),
                "current_memory_free_in_mib_unit" => intdiv($latestMemoryUsage->available, 1024)
            ]);
        }
    }

    private function storeDiskStatus(Request $request, ComputeNode $computeNode)
    {
        $computeNodeId = $computeNode->id;

        $diskStatus = $request->data["nodeStatus"]["disk"];
        foreach ($diskStatus["ioUsage"] as $ioUsage) {
            $attributes = [
                "compute_node_id" => $computeNodeId,
                "id" => $ioUsage["basic_disk_statistics_id"],
                "block_device" => $ioUsage["block_device"],
            ];
            $values = [
                "read_bytes_per_second" => $ioUsage["read_bytes_per_second"],
                "write_bytes_per_second" => $ioUsage["write_bytes_per_second"],
                "microtime" => $ioUsage["basic_disk_statistics"]["microtime"],
            ];
            DiskIOUsage::query()->updateOrInsert($attributes, $values);
        }

        foreach ($diskStatus["spaceUsage"] as $spaceUsage) {
            $attributes = [
                "compute_node_id" => $computeNodeId,
                "id" => $spaceUsage["id"],
            ];
            $values = [
                "total" => $spaceUsage["total"],
                "free" => $spaceUsage["free"],
                "microtime" => $spaceUsage["microtime"],
            ];
            DiskSpaceUsage::query()->updateOrInsert($attributes, $values);
        }

        if ($diskSpaceUsage = DiskSpaceUsage::query()->where("compute_node_id", $computeNodeId)->orderByDesc("microtime")->limit(1)->first()) {
            $computeNode->fill([
                "total_disk_capacity_in_gib_unit" => $diskSpaceUsage->total / 1073741824,
                "current_disk_free_in_gib_unit" => $diskSpaceUsage->free / 1073741824,
            ]);
        }
    }

    private function storeComputeInstanceDiskIOUsages($ioUsages, ComputeInstance $computeInstance)
    {
        $computeInstanceId = $computeInstance->id;

        foreach ($ioUsages as $ioUsage) {
            $attributes = [
                "compute_instance_id" => $computeInstanceId,
                "id" => $ioUsage["basic_disk_statistics_id"],
            ];
            $values = [
                "rd_req_per_second" => $ioUsage["rd_req_per_second"],
                "rd_bytes_per_second" => $ioUsage["rd_bytes_per_second"],
                "wr_req_per_second" => $ioUsage["wr_req_per_second"],
                "wr_bytes_per_second" => $ioUsage["wr_bytes_per_second"],
                "microtime" => $ioUsage["basic_disk_statistics"]["microtime"],
            ];
            ComputeInstance\DiskIOUsage::query()->updateOrInsert($attributes, $values);
        }
    }

    private function storeLoadAverage(Request $request, ComputeNode $computeNode)
    {
        $computeNodeId = $computeNode->id;

        foreach ($request->data["nodeStatus"]["loadAverage"] as $loadAverage) {
            $attributes = [
                "compute_node_id" => $computeNodeId,
                "id" => $loadAverage["id"],
            ];
            $values = [
                "one_minute_average" => $loadAverage["one_minute_average"],
                "five_minutes_average" => $loadAverage["five_minutes_average"],
                "fifteen_minutes_average" => $loadAverage["fifteen_minutes_average"],
                "microtime" => $loadAverage["microtime"],
            ];
            LoadAverage::query()->updateOrInsert($attributes, $values);
        }
    }

    private function storeNetworkStatus(Request $request, ComputeNode $computeNode)
    {
        $computeNodeId = $computeNode->id;

        $networkStatus = $request->data["nodeStatus"]["network"];
        foreach ($networkStatus["bandwidthUsage"] as $bandwidthUsage) {
            $attributes = [
                "compute_node_id" => $computeNodeId,
                "id" => $bandwidthUsage["basic_network_statistics_id"],
                "network_device" => $bandwidthUsage["network_device"],
            ];
            $values = [
                "rx_bytes_per_second" => $bandwidthUsage["rx_bytes_per_second"],
                "rx_packets_per_second" => $bandwidthUsage["rx_packets_per_second"],
                "tx_bytes_per_second" => $bandwidthUsage["tx_bytes_per_second"],
                "tx_packets_per_second" => $bandwidthUsage["tx_packets_per_second"],
                "microtime" => $bandwidthUsage["basic_network_statistics"]["microtime"],
            ];
            BandwidthUsage::query()->updateOrInsert($attributes, $values);
        }
    }

    private function storeComputeInstanceNetworkUsages($networkInterfaces, ComputeInstance $computeInstance)
    {
        $networkInterfaceIdKeyByType = $computeInstance->networkInterfaces()->get()->pluck("id", "type")->toArray();

        $trafficShareGroupId = $computeInstance->node->zone->traffic_share_group_id;

        foreach ($networkInterfaces as $networkInterface) {
            if (!array_key_exists($networkInterface["type"], $networkInterfaceIdKeyByType))
                continue;
            $networkInterfaceId = $networkInterfaceIdKeyByType[$networkInterface["type"]];
            $this->storeNetworkInterfaceTrafficUsages($networkInterface["traffic_usages"], $computeInstance->user_id, $networkInterfaceId, $networkInterface["type"], $trafficShareGroupId);
            $this->storeNetworkInterfaceBandwidthUsages($networkInterface["bandwidth_usages"], $networkInterfaceId);
        }
    }

    private function storeNetworkInterfaceTrafficUsages($trafficUsages, $userId, $networkInterfaceId, $networkType, $trafficShareGroupId)
    {
        foreach ($trafficUsages as $trafficUsage) {
            $attributes = [
                "network_interface_id" => $networkInterfaceId,
                "id" => $trafficUsage["id"],
            ];
            $values = [
                "user_id" => $userId,
                "network_type" => $networkType,
                "traffic_share_group_id" => $trafficShareGroupId,
                "rx_byte_count" => $trafficUsage["rx_byte_count"],
                "rx_packet_count" => $trafficUsage["rx_packet_count"],
                "tx_byte_count" => $trafficUsage["tx_byte_count"],
                "tx_packet_count" => $trafficUsage["tx_packet_count"],
                "microtime" => $trafficUsage["microtime"],
            ];

            try {
                ComputeInstance\TrafficUsage::query()->updateOrInsert($attributes, $values);
            } catch (QueryException $e) {
                if ($e->getCode() !== 23000)
                    throw $e;
            }
        }
    }

    private function storeNetworkInterfaceBandwidthUsages($bandwidthUsages, $networkInterfaceId)
    {
        foreach ($bandwidthUsages as $bandwidthUsage) {
            $attributes = [
                "network_interface_id" => $networkInterfaceId,
                "id" => $bandwidthUsage["basic_traffic_usage_id"],
            ];
            $values = [
                "rx_bytes_per_second" => $bandwidthUsage["rx_bytes_per_second"],
                "rx_packets_per_second" => $bandwidthUsage["rx_packets_per_second"],
                "tx_bytes_per_second" => $bandwidthUsage["tx_bytes_per_second"],
                "tx_packets_per_second" => $bandwidthUsage["tx_packets_per_second"],
                "microtime" => $bandwidthUsage["basic_traffic_usage"]["microtime"],
            ];
            ComputeInstance\BandwidthUsage::query()->updateOrInsert($attributes, $values);
        }
    }
}
