<?php

namespace App\Http\Controllers\AdminAPI\Node\ComputeNode;

use App\Http\Controllers\ModelControllers\FilterableIndexController;
use App\Node\ComputeNode;
use App\Region;
use App\Utils\Time;
use App\Zone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ComputeNodeIndexController extends FilterableIndexController
{
    protected $sortableColumns = [
        "id" => true,
        "name" => true,
        "description" => true,
        "host" => true,
        "status" => true,
        "cpu_utilization" => true,
        "current_disk_free_in_gib_unit" => true,
        "current_memory_free_in_mib_unit" => true,
    ];

    protected $equalSearchColumns = [
        "id",
        "host",
    ];

    protected $fulltextSearchColumns = [
        "name",
    ];

    public function __construct()
    {
        $this->middleware("can:index," . ComputeNode::class);
        $this->middleware("can:index," . Region::class)->only(["listRegions"]);
    }

    public function __invoke(Request $request)
    {
        return [
            "result" => true,
            "availableRegions" => Region::all()->keyBy("id"),
            "availableZones" => Zone::all()->keyBy("id"),
            "computeNodes" => $this->paginate($request, ComputeNode::query()),
            "serverTime" => Time::getServerTime(),
        ];
    }

    public function listRegions()
    {
        return [
            "result" => true,
            "availableRegions" => Region::all()->keyBy("id"),
            "availableZones" => Zone::all()->keyBy("id"),
            "serverTime" => Time::getServerTime(),
        ];
    }
}
