<?php

namespace App\Http\Controllers\IPPool\IPv6;

use App\Http\Controllers\IPPool\IPPoolUpdateMiddleware;
use App\IPPool\IPv6;
use App\Zone;
use App\ZoneHasIPv6Pool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ZoneAssignController extends Controller
{
    use IPPoolUpdateMiddleware;

    public function assign(IPv6 $IPv6, Zone $zone)
    {
        ZoneHasIPv6Pool::query()->create([
            "pool_id" => $IPv6->id,
            "zone_id" => $zone->id,
        ]);

        return ["result" => true, "poolId" => $IPv6->id, "zoneId" => $zone->id];
    }

    public function destroy(IPv6 $IPv6, Zone $zone)
    {
        ZoneHasIPv6Pool::query()->where([
            "pool_id" => $IPv6->id,
            "zone_id" => $zone->id,
        ])->delete();

        return ["result" => true];
    }

    public function massDestroy(Request $request, IPv6 $IPv6)
    {
        if ($request->has("items") && is_array($items = $request->items)) {
            $deletedItemCount = ZoneHasIPv6Pool::query()->where("pool_id", $IPv6->id)->whereIn("zone_id", $items)->delete();
        } else {
            $deletedItemCount = 0;
            $items = [];
        }

        return ["result" => true, "items" => $items, "deletedItemCount" => $deletedItemCount];
    }
}
