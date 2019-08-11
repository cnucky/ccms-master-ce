<?php

namespace App\Http\Controllers\AdminAPI\ImageCategory;

use App\Constants\ImageStatusCode;
use App\Image;
use App\ImageCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class ImageCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware("can:index," . Image::class)->only([
            "edit",
            "show",
        ]);
        $this->middleware("can:create," . Image::class)->only([
            "store",
        ]);
        $this->middleware("can:update," . Image::class)->only([
            "update",
        ]);
        $this->middleware("can:delete," . Image::class)->only([
            "massDestroy",
            "destroy",
        ]);
    }

    public function store(Request $request)
    {
        $this->storeValidate($request);

        $imageCategory = ImageCategory::query()->create($this->retrieveValues($request));

        return ["result" => true, "imageCategory" => $imageCategory];
    }

    public function update(Request $request, ImageCategory $imageCategory)
    {
        $this->storeValidate($request, $imageCategory);

        $imageCategory->update($this->retrieveValues($request));

        return ["result" => true, "imageCategory" => $imageCategory];
    }

    public function destroy(ImageCategory $imageCategory)
    {
        $imageCategory->delete();

        return ["result" => true];
    }

    private function storeValidate(Request $request, ImageCategory $except = null)
    {
        $nameUniqueValidateRule = Rule::unique("image_categories", "name");
        if (!is_null($except)) {
            $nameUniqueValidateRule->ignore($except->id);
        }
        $this->validate($request, [
            "name" => ["required", "max:32", $nameUniqueValidateRule],
            "icon_class" => "nullable|max:16",
            "order" => "nullable|integer|min:-32768|max:32767",
            "status" => ["required", Rule::in(ImageStatusCode::AVAILABLE_STATUS_CODES)]
        ]);
    }

    private function retrieveValues(Request $request)
    {
        return [
            "name" => $request->name,
            "icon_class" => $request->icon_class,
            "order" => $request->order ? $request->order : 0,
            "status" => $request->status,
        ];
    }
}
