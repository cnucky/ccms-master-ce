<?php

namespace App\Http\Controllers\AdminAPI;

use App\Http\Controllers\ModelControllers\FilterableIndexController;
use App\Region;
use App\Zone;
use Illuminate\Http\Request;

class ZoneIndexController extends FilterableIndexController
{
    protected $sortableColumns = [
        "id" => true,
        "name" => true,
        "description" => true,
        "created_at" => true,
        "updated_at" => true,
    ];

    protected $equalSearchColumns = [
        "id"
    ];

    protected $fulltextSearchColumns = [
        "name"
    ];

    public function __construct()
    {
        $this->middleware("can:index," . Zone::class);
    }

    public function __invoke(Request $request)
    {
        $query = Zone::query();

        if ($request->has("region")) {
            $query->where("region_id", "=", $request->region);
        }

        $zones = $this->paginate($request, $query->with("region")->withCount("computeNodes"));
        foreach ($zones as $zone) {
            $zone->append("resourceCounters");
        }

        return ["result" => true, "zones" => $zones, "availableRegions" => Region::all(["id", "name", "icon_class"])];
    }
}
