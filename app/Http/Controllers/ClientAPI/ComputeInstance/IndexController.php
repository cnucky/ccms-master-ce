<?php

namespace App\Http\Controllers\ClientAPI\ComputeInstance;

use App\ComputeInstance;
use App\Http\Controllers\ComputeInstance\ComputeInstanceControllerUtils;
use App\Http\Controllers\ModelControllers\FilterableIndexController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use YunInternet\CCMSCommon\Constants\NetworkType;

class IndexController extends FilterableIndexController
{
    protected $sortableColumns = [
        "name" => true,
        "created_at" => true,
        "status" => true,
        "twenty_minutes_average_cpu_utilization" => true,
        "twenty_minutes_average_public_network_rx_bytes_per_second" => true,
        "twenty_minutes_average_public_network_tx_bytes_per_second" => true,
        "twenty_minutes_average_private_network_rx_bytes_per_second" => true,
        "twenty_minutes_average_private_network_tx_bytes_per_second" => true,
        "twenty_minutes_average_disk_read_bytes_per_second" => true,
        "twenty_minutes_average_disk_write_bytes_per_second" => true,
        "twenty_minutes_average_disk_read_req_per_second" => true,
        "twenty_minutes_average_disk_write_req_per_second" => true,
    ];

    protected $fulltextSearchColumns = [
        "unique_id",
        "name",
    ];

    public function index(Request $request)
    {
        return [
            "result" => true,
            "instances" => $this->paginate($request, ComputeInstance::query()
                ->where(ComputeInstanceControllerUtils::whereCurrentUserInstancesClosure($request))
                ->with([
                "processingOperationRequests",
                "node:id,name,zone_id",
                "node.zone:id,name,region_id",
                "node.zone.region:id,name,icon_class",
                "networkInterfaces" => function ($builder) {
                    $builder
                        ->where("type", NetworkType::TYPE_PUBLIC_NETWORK)
                    ;
                },
                "networkInterfaces.ipv4Addresses",
                "networkInterfaces.ipv6Addresses",
            ])),
        ];
    }

    public function availableInstances(Request $request)
    {
        return ["result" => true, "availableInstances" => ComputeInstance::query()
            ->where(ComputeInstanceControllerUtils::whereCurrentUserInstancesClosure($request))
            ->with(["node:id,zone_id", "node.zone:id,storage_price_per_hour_per_gib"])
            ->get(["id", "unique_id", "name", "compute_node_id"])
        ];
    }
}
