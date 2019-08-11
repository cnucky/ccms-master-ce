<?php

namespace App\Http\Controllers\AdminAPI;

use App\Constants\GlobalErrorCode;
use App\Constants\StatusCode;
use App\Region;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class RegionController extends Controller
{
    const ALLOW_SORT_BY_COLUMN = [
        "id" => true,
        "name" => true,
        "description" => true,
        "created_at" => true,
        "updated_at" => true,
    ];

    public function __construct()
    {
        $this->middleware("can:index," . Region::class)->only([
            "edit",
            "index",
        ]);
        $this->middleware("can:create," . Region::class)->only([
            "store",
        ]);
        $this->middleware("can:update," . Region::class)->only([
            "update",
        ]);
        $this->middleware("can:delete," . Region::class)->only([
            "massDestroy",
            "destroy",
        ]);
    }

    public function index(Request $request)
    {
        $query = Region::query();

        if ($request->has("search") && !empty($request->search)) {
            $query->where(function (Builder $builder) use ($request) {
                $builder->where("id", "=", $request->search)->orWhere("name", "LIKE", "%" . $request->search . "%");
            });
        }

        if ($request->has("sortKey") && $request->has("isAsc")) {
            if (array_key_exists($request->sortKey, self::ALLOW_SORT_BY_COLUMN)) {
                if ($request->isAsc)
                    $query->orderBy($request->sortKey);
                else
                    $query->orderByDesc($request->sortKey);
            }
        }

        return ["result" => true, "regions" => $query->paginate(is_numeric($request->prePage) ? $request->prePage : 15)];
    }

    public function store(Request $request)
    {
        $this->storeValidate($request);

        $item = Region::query()->create([
            "name" => $request->name,
            "icon_class" => $request->icon_class,
            "description" => is_null($request->description) ? "" : $request->description,
            "status" => StatusCode::STATUS_NORMAL,
        ]);

        return ["result" => true, "item" => $item];
    }

    public function update(Request $request, Region $region)
    {
        $this->storeValidate($request, $region);
        $region->update([
            "name" => $request->name,
            "icon_class" => $request->icon_class,
            "description" => $request->description,
        ]);

        return ["result" => true, "item" => $region];
    }

    public function destroy(Region $region)
    {
        if ($region->computeNodes()->count()) {
            return ["result" => false, "errno" => GlobalErrorCode::HAS_NODES];
        }

        $id = $region->id;

        $region->delete();

        return ["result" => true, "item" => $id];
    }

    public function massDestroy(Request $request)
    {
        if ($request->has("items") && is_array($items = $request->items)) {
            $deletedItemCount = Region::query()->whereIn("id", $items)->delete();
        } else {
            $deletedItemCount = 0;
            $items = [];
        }

        return ["result" => true, "items" => $items, "deletedItemCount" => $deletedItemCount];
    }

    private function storeValidate(Request $request, Region $except = null)
    {
        $nameValidateRule = Rule::unique("regions", "name");
        if (!is_null($except)) {
            $nameValidateRule->ignore($except->id);
        }
        $this->validate($request, [
            "name" => ["required", "max:32", $nameValidateRule],
            "icon_class" => "nullable|max:16",
            "description" => "nullable|max:255",
        ]);
    }
}
