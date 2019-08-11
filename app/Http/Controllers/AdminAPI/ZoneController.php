<?php

namespace App\Http\Controllers\AdminAPI;

use App\ComputeInstance;
use App\Constants\Constants;
use App\Constants\GlobalErrorCode;
use App\Constants\StatusCode;
use App\Http\Controllers\ModelControllers\FilterableIndexController;
use App\Http\Controllers\ModelControllers\MassDestroyable;
use App\Node\ComputeNode;
use App\Region;
use App\Zone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ZoneController extends Controller
{
    use MassDestroyable;

    public function __construct()
    {
        $this->middleware("can:index," . Zone::class)->only([
            "edit",
            "show",
        ]);
        $this->middleware("can:create," . Zone::class)->only([
            "store",
        ]);
        $this->middleware("can:update," . Zone::class)->only([
            "update",
        ]);
        $this->middleware("can:delete," . Zone::class)->only([
            "massDestroy",
            "destroy",
        ]);
    }

    public function show(Request $request, $zone)
    {
        /**
         * @var Zone $zoneModel
         */
        $zoneModel = Zone::query()
            ->with("region")
            ->with("computeNodes")
            ->with("ipv4Pools")
            ->with("ipv6Pools")
            ->findOrFail($zone)
        ;

        $offlineNodeCount = $zoneModel->computeNodes()->where("last_communicated_at", "<", date("Y-m-d H:i:s", time() - 600))->count();

        $nodeList = $zoneModel->computeNodes()->pluck("id");
        $computeInstanceCount = ComputeInstance::query()->whereIn("compute_node_id", $nodeList)->count();

        $result = $zoneModel->getResourceCountersAttribute();

        return [
            "result" => true,
            "data" => [
                "zone" => $zoneModel,
                "computeInstanceCount" => $computeInstanceCount,
                "totalMemoryCapacity" => $result->zone_total_memory_capacity,
                "totalDiskCapacity" => $result->zone_total_disk_capacity,
                "allocatedMemoryCapacity" => $result->zone_total_allocated_memory_capacity,
                "allocatedDiskCapacity" => $result->zone_total_allocated_disk_capacity,
                "offlineNodeCount" => $offlineNodeCount,
                "serverTime" => date("Y-m-d H:i:s"),
            ]
        ];
    }

    public function store(Request $request)
    {
        $values = $this->storeValidate($request);
        $values["traffic_share_group_id"] = 1;

        $item = Zone::query()->create($values);

        // With region
        $item->region;

        return ["result" => true, "item" => $item];
    }

    public function update(Request $request, Zone $zone)
    {
        $values = $this->storeValidate($request, $zone);

        $zone->update($values);

        // With region
        $zone->region;

        return ["result" => true, "item" => $zone];
    }

    public function destroy(Zone $zone)
    {
        if ($zone->computeNodes()->count()) {
            return ["result" => false, "errno" => GlobalErrorCode::HAS_NODES];
        }

        $zone->delete();

        return ["result" => true];
    }

    protected function modelQuery()
    {
        return Zone::query();
    }

    private function storeValidate(Request $request, Zone $except = null)
    {
        $nameUniqueValidateRule = Rule::unique("zones", "name")->where("region_id", $request->region_id);
        if (!is_null($except)) {
            $nameUniqueValidateRule->ignore($except->id);
        }

        $this->validate($request, [
            "region_id" => "required|exists:regions,id",
            "name" => ["required", "max:32", $nameUniqueValidateRule],
            "storage_price_per_hour_per_gib" => Constants::DECIMAL_8_4_PRICE_VALIDATE_RULE,
            "description" => "nullable|max:255",
        ]);

        return [
            "region_id" => $request->region_id,
            "name" => $request->name,
            "storage_price_per_hour_per_gib" => is_null($request->storage_price_per_hour_per_gib) ? "0" : $request->storage_price_per_hour_per_gib,
            "description" => $request->description,
            "status" => StatusCode::STATUS_NORMAL,
        ];
    }
}
