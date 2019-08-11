<?php

namespace App\Http\Controllers\IPPool\IPv4;

use App\Http\Controllers\IPPool\IPPoolUpdateMiddleware;
use App\IPPool\IPv4;
use App\Zone;
use App\ZoneHasIPv4Pool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ZoneAssignController extends Controller
{
    use IPPoolUpdateMiddleware;

    public function assign(IPv4 $IPv4, Zone $zone)
    {
        ZoneHasIPv4Pool::query()->create([
            "pool_id" => $IPv4->id,
            "zone_id" => $zone->id,
        ]);

        return ["result" => true, "poolId" => $IPv4->id, "zoneId" => $zone->id];
    }

    public function destroy(IPv4 $IPv4, Zone $zone)
    {
        ZoneHasIPv4Pool::query()->where([
            "pool_id" => $IPv4->id,
            "zone_id" => $zone->id,
        ])->delete();

        return ["result" => true];
    }

    public function massDestroy(Request $request, IPv4 $IPv4)
    {
        if ($request->has("items") && is_array($items = $request->items)) {
            $deletedItemCount = ZoneHasIPv4Pool::query()->where("pool_id", $IPv4->id)->whereIn("zone_id", $items)->delete();
        } else {
            $deletedItemCount = 0;
            $items = [];
        }

        return ["result" => true, "items" => $items, "deletedItemCount" => $deletedItemCount];
    }
}
