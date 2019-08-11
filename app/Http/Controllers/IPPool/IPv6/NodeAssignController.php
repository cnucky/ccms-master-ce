<?php

namespace App\Http\Controllers\IPPool\IPv6;

use App\Http\Controllers\IPPool\IPPoolUpdateMiddleware;
use App\IPPool\IPv6;
use App\Node\ComputeNode;
use App\NodeHasIPv6Pool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NodeAssignController extends Controller
{
    use IPPoolUpdateMiddleware;

    public function assign(IPv6 $IPv6, ComputeNode $node)
    {
        NodeHasIPv6Pool::query()->create([
            "pool_id" => $IPv6->id,
            "node_id" => $node->id,
        ]);

        return ["result" => true, "poolId" => $IPv6->id, "nodeId" => $node->id];
    }

    public function destroy(IPv6 $IPv6, ComputeNode $node)
    {
        NodeHasIPv6Pool::query()->where([
            "pool_id" => $IPv6->id,
            "node_id" => $node->id,
        ])->delete();

        return ["result" => true];
    }

    public function massDestroy(Request $request, IPv6 $IPv6)
    {
        if ($request->has("items") && is_array($items = $request->items)) {
            $deletedItemCount = NodeHasIPv6Pool::query()->where("pool_id", $IPv6->id)->whereIn("node_id", $items)->delete();
        } else {
            $deletedItemCount = 0;
            $items = [];
        }

        return ["result" => true, "items" => $items, "deletedItemCount" => $deletedItemCount];
    }
}
