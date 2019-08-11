<?php

namespace App\Http\Controllers\AdminAPI\PublicFloppyCategory;

use App\Http\Controllers\AdminAPI\ImageCommonCategoryValidator;
use App\PublicFloppy;
use App\PublicFloppyCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PublicFloppyCategoryController extends Controller
{
    use ImageCommonCategoryValidator;

    public function __construct()
    {
        $this->middleware("can:index," . PublicFloppy::class)->only([
            "edit",
            "show",
        ]);
        $this->middleware("can:create," . PublicFloppy::class)->only([
            "store",
        ]);
        $this->middleware("can:update," . PublicFloppy::class)->only([
            "update",
        ]);
        $this->middleware("can:delete," . PublicFloppy::class)->only([
            "massDestroy",
            "destroy",
        ]);
    }

    public function store(Request $request)
    {
        $this->storeValidate($request, "public_floppy_categories");
        $values = $this->retrieveValues($request);

        return ["result" => true, "category" => PublicFloppyCategory::query()->create($values)];
    }

    public function update(Request $request, PublicFloppyCategory $publicFloppyCategory)
    {
        $this->storeValidate($request, "public_floppy_categories", $publicFloppyCategory);
        $values = $this->retrieveValues($request);
        $publicFloppyCategory->update($values);

        return ["result" => true, "category" => $publicFloppyCategory];
    }

    public function destroy(PublicFloppyCategory $PublicFloppyCategory)
    {
        $PublicFloppyCategory->delete();

        return ["result" => true];
    }
}
