<?php

namespace App\Http\Controllers\IPPool\IPv4;

use App\Http\Controllers\IPPool\IPPoolUpdateMiddleware;
use App\IPPool\IPv4;
use App\Node\ComputeNode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NodeAssignIndexController extends Controller
{
    use IPPoolUpdateMiddleware;

    public function __invoke(IPv4 $IPv4)
    {
        return ["result" => true, "assignments" => $IPv4->nodeAssignments()->with(["zone", "zone.region"])->get()->keyBy("id"), "availableNodes" => ComputeNode::query()->with(["zone", "zone.region"])->get()->keyBy("id")];
    }
}
