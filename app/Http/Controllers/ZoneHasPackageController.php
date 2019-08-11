<?php

namespace App\Http\Controllers;

use App\ComputeInstancePackage;
use App\ComputeInstancePackageCategory;
use App\Http\Controllers\ModelControllers\MassDestroyable;
use App\Zone;
use App\ZoneHasComputeInstancePackage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use YunInternet\CCMSCommon\Constants\Constants;

class ZoneHasPackageController extends Controller
{
    public function index(Request $request, Zone $zone)
    {
        return [
            "result" => true,
            "packages" => $zone
                ->packages()
                ->with("category")
                ->get()
                ->keyBy("id"),
            "availablePackages" => ComputeInstancePackageCategory::with("packages")->get(),
        ];
    }

    public function assignPackage(Request $request, Zone $zone, ComputeInstancePackage $computeInstancePackage)
    {
        $assignment = ZoneHasComputeInstancePackage::query()->create([
            "zone_id" => $zone->id,
            "package_id" => $computeInstancePackage->id,
        ]);

        return ["result" => true, "assignmentId" => $assignment->id];
    }

    public function updatePackage(Request $request, ZoneHasComputeInstancePackage $zoneHasComputeInstancePackage)
    {
        $this->validate($request, [
            "stock" => "nullable|integer|min:0|max:" . Constants::UINT32_MAX,
        ]);

        $zoneHasComputeInstancePackage->update([
            "stock" => $request->stock,
        ]);

        return ["result" => true];
    }

    public function deletePackage(ZoneHasComputeInstancePackage $zoneHasComputeInstancePackage)
    {
        $zoneHasComputeInstancePackage->delete();
        return ["result" => true];
    }

    public function massDestroy(Request $request, Zone $zone)
    {
        if ($request->has("items") && is_array($items = $request->items)) {
            $deletedItemCount = $this->modelQuery()->where("zone_id", $zone->id)->whereIn("package_id", $items)->delete();
        } else {
            $deletedItemCount = 0;
            $items = [];
        }

        return ["result" => true, "items" => $items, "deletedItemCount" => $deletedItemCount];
    }

    /**
     * @inheritDoc
     */
    protected function modelQuery()
    {
        return ZoneHasComputeInstancePackage::query();
    }
}
