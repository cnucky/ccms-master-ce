<?php

namespace App\Http\Controllers\ComputeInstance;

use App\ComputeInstance;
use App\Http\Controllers\ModelControllers\FilterableIndexController;
use App\Node\ComputeNode;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use YunInternet\CCMSCommon\Constants\NetworkType;

class AdminIndexController extends FilterableIndexController
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

    protected $equalSearchColumns = [
        "id",
    ];

    protected $leftMatchSearchColumns = [
        "unique_id",
        "name",
    ];

    public function index(Request $request)
    {
        return [
            "result" => true,
            "instances" => $this->paginate($request, $this->getBasicQueryBuilder($request)),
        ];
    }

    public function indexByNode(Request $request, ComputeNode $computeNode)
    {
        return [
            "result" => true,
            "instances" => $this->paginate($request, $this->getBasicQueryBuilder($request)->where("compute_node_id", $computeNode->id)),
        ];
    }

    public function indexByUser(Request $request, User $user)
    {
        return [
            "result" => true,
            "instances" => $this->paginate($request, $this->getBasicQueryBuilder($request)->where("user_id", $user->id)),
        ];
    }

    private function getBasicQueryBuilder(Request $request)
    {
        $query = ComputeInstance::query()
            ->with([
                "user",
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
            ]);
        if (is_numeric($request->status)) {
            $query->where("status", $request->status);
        }
        return $query;
    }
}
