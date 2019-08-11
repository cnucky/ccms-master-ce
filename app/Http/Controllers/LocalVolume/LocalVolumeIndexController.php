<?php

namespace App\Http\Controllers\LocalVolume;

use App\ComputeInstance;
use App\Http\Controllers\ModelControllers\FilterableIndexController;
use App\Node\ComputeNode;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LocalVolumeIndexController extends FilterableIndexController
{
    use \App\Http\Controllers\LocalVolume\Controller;

    protected $equalSearchColumns = [
        "id",
    ];

    protected $leftMatchSearchColumns = ["unique_id"];

    protected $sortableColumns = [
        "unique_id" => true,
        "capacity" => true,
        "bus" => true,
        "attached_compute_instance_id" => true,
        "created_at" => true,
    ];

    public function index(Request $request)
    {
        return ["result" => true, "volumes" => $this->paginate($request, ComputeInstance\LocalVolume::query()->with("instance", "processingOperationRequests", "node:id,zone_id", "node.zone.region")->where(self::whereBelong2UserClosure($request)))];
    }

    public function indexByComputeInstance(Request $request, ComputeInstance $computeInstance)
    {
        return ["result" => true, "volumes" => $this->paginate($request, ComputeInstance\LocalVolume::query()->where("attached_compute_instance_id", $computeInstance->id)->with("processingOperationRequests"))];
    }

    public function attachableInstances(ComputeInstance\LocalVolume $localVolume)
    {
        return [
            "result" => true,
            "attachableInstances" => ComputeInstance::query()
                ->where("user_id", $localVolume->user_id)
                ->where("compute_node_id", $localVolume->compute_node_id)
                ->get()
        ];
    }

    public function indexForAdmin(Request $request)
    {
        return [
            "result" => true,
            "volumes" => $this->paginate($request, ComputeInstance\LocalVolume::query()
                ->with(["instance", "processingOperationRequests", "node.zone.region", "user"])
            )
        ];
    }

    public function indexByNode(Request $request, ComputeNode $computeNode)
    {
        return [
            "result" => true,
            "volumes" => $this->paginate($request, ComputeInstance\LocalVolume::query()
                ->where("compute_node_id", $computeNode->id)
                ->with(["instance", "processingOperationRequests", "node.zone.region", "user"])
            )
        ];
    }

    public function indexByUser(Request $request, User $user)
    {
        return [
            "result" => true,
            "volumes" => $this->paginate($request, ComputeInstance\LocalVolume::query()
                ->where("user_id", $user->id)
                ->with(["instance", "processingOperationRequests", "node.zone.region", "user"])
            )
        ];
    }
}
