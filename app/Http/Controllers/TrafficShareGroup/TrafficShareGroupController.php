<?php

namespace App\Http\Controllers\TrafficShareGroup;

use App\ComputeInstance\TrafficUsage;
use App\Constants\Constants;
use App\Constants\GlobalErrorCode;
use App\Region;
use App\TrafficShareGroup;
use App\Zone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class TrafficShareGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware("can:index," . TrafficShareGroup::class)->only([
            "edit",
            "show",
            "zoneIndex",
        ]);
        $this->middleware("can:create," . TrafficShareGroup::class)->only([
            "store",
        ]);
        $this->middleware("can:update," . TrafficShareGroup::class)->only([
            "update",
            "assignZone",
        ]);
        $this->middleware("can:delete," . TrafficShareGroup::class)->only([
            "massDestroy",
            "destroy",
        ]);
        $this->middleware("can:index," . Zone::class)->only([
            "zoneIndex"
        ]);
    }

    public function show(Request $request, TrafficShareGroup $trafficShareGroup)
    {
        return ["result" => true, "trafficShareGroup" => $trafficShareGroup];
    }

    public function zoneIndex(Request $request, TrafficShareGroup $trafficShareGroup)
    {
        return ["result" => true, "zones" => $trafficShareGroup->zones()->get()->keyBy("id"), "availableZones" => Region::query()->with("zones")->get()];
    }

    public function store(Request $request)
    {
        $this->storeValidate($request);
        return ["result" => true, "trafficShareGroup" => TrafficShareGroup::query()->create($this->retrieveValues($request))];
    }

    public function update(Request $request, TrafficShareGroup $trafficShareGroup)
    {
        $this->storeValidate($request, $trafficShareGroup);
        $trafficShareGroup->update($this->retrieveValues($request));
        return $this->show($request, $trafficShareGroup);
    }

    public function assignZone(TrafficShareGroup $trafficShareGroup, Zone $zone)
    {
        $zone->update([
            "traffic_share_group_id" => $trafficShareGroup->id,
        ]);

        return ["result" => true];
    }

    public function destroy(TrafficShareGroup $trafficShareGroup)
    {
        if ($trafficShareGroup->id === 1)
            return ["result" => false];
        Zone::query()->where("traffic_share_group_id", $trafficShareGroup->id)->update([
            "traffic_share_group_id" => 1,
        ]);
        TrafficUsage::query()
            ->where("traffic_share_group_id", $trafficShareGroup->id)
            ->whereNull("charging_started_at")
            ->update([
                "traffic_share_group_id" => 1,
            ])
        ;
        $trafficShareGroup->delete();
        return ["result" => true];
    }

    private function storeValidate(Request $request, TrafficShareGroup $except = null)
    {
        $nameValidateRule = Rule::unique("traffic_share_groups", "name");
        if (!is_null($except))
            $nameValidateRule->ignore($except->id);
        $this->validate($request, [
            "name" => [
                "required",
                "max:32",
                $nameValidateRule,
            ],
            "description" => "nullable|max:255",
            // "price_per_rx_gib" => Constants::DECIMAL_8_4_PRICE_VALIDATE_RULE,
            "price_per_tx_gib" => Constants::DECIMAL_8_4_PRICE_VALIDATE_RULE,
        ]);
    }

    private function retrieveValues(Request $request)
    {
        return [
            "name" => $request->name,
            "description" => $request->description,
            // "price_per_rx_gib" => $request->price_per_rx_gib,
            "price_per_rx_gib" => "0",
            "price_per_tx_gib" => $request->price_per_tx_gib,
        ];
    }
}
