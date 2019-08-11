<?php

namespace App\Http\Controllers\AdminAPI\PublicISOCategory;

use App\Constants\CommonStatusCode;
use App\Http\Controllers\AdminAPI\ImageCommonCategoryValidator;
use App\PublicISO;
use App\PublicISOCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class PublicISOCategoryController extends Controller
{
    use ImageCommonCategoryValidator;

    public function __construct()
    {
        $this->middleware("can:index," . PublicISO::class)->only([
            "edit",
            "show",
        ]);
        $this->middleware("can:create," . PublicISO::class)->only([
            "store",
        ]);
        $this->middleware("can:update," . PublicISO::class)->only([
            "update",
        ]);
        $this->middleware("can:delete," . PublicISO::class)->only([
            "massDestroy",
            "destroy",
        ]);
    }

    public function store(Request $request)
    {
        $this->storeValidate($request, "public_iso_categories");
        $values = $this->retrieveValues($request);

        return ["result" => true, "category" => PublicISOCategory::query()->create($values)];
    }

    public function update(Request $request, PublicISOCategory $publicISOCategory)
    {
        $this->storeValidate($request, "public_iso_categories", $publicISOCategory);
        $values = $this->retrieveValues($request);
        $publicISOCategory->update($values);

        return ["result" => true, "category" => $publicISOCategory];
    }

    public function destroy(PublicISOCategory $publicISOCategory)
    {
        $publicISOCategory->delete();

        return ["result" => true];
    }
}
