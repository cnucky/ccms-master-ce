<?php

namespace App\Http\Controllers\IPPool\IPv4;

use App\Http\Controllers\IPPool\IPPoolUpdateMiddleware;
use App\IPPool\IPv4;
use App\Node\ComputeNode;
use App\NodeHasIPv4Pool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NodeAssignController extends Controller
{
    use IPPoolUpdateMiddleware;

    public function assign(IPv4 $IPv4, ComputeNode $node)
    {
        NodeHasIPv4Pool::query()->create([
            "pool_id" => $IPv4->id,
            "node_id" => $node->id,
        ]);

        return ["result" => true, "poolId" => $IPv4->id, "nodeId" => $node->id];
    }

    public function destroy(IPv4 $IPv4, ComputeNode $node)
    {
        NodeHasIPv4Pool::query()->where([
            "pool_id" => $IPv4->id,
            "node_id" => $node->id,
        ])->delete();

        return ["result" => true];
    }

    public function massDestroy(Request $request, IPv4 $IPv4)
    {
        if ($request->has("items") && is_array($items = $request->items)) {
            $deletedItemCount = NodeHasIPv4Pool::query()->where("pool_id", $IPv4->id)->whereIn("node_id", $items)->delete();
        } else {
            $deletedItemCount = 0;
            $items = [];
        }

        return ["result" => true, "items" => $items, "deletedItemCount" => $deletedItemCount];
    }
}
