<?php

namespace App\Http\Controllers\AdminAPI\Image;

use App\Constants\ImageStatusCode;
use App\Http\Controllers\ModelControllers\MassDestroyable;
use App\Image;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use YunInternet\CCMSCommon\Constants\Constants;

class ImageController extends Controller
{
    use MassDestroyable;

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

        $image = Image::query()->create($this->retrieveValues($request));

        return ["result" => true, "image" => $image];
    }

    public function update(Request $request, Image $image)
    {
        $this->storeValidate($request, $image);

        $image->update($this->retrieveValues($request));

        return ["result" => true, "image" => $image];
    }

    public function destroy(Image $image)
    {
        $image->delete();

        return ["result" => true];
    }

    protected function modelQuery()
    {
        return Image::query();
    }


    private function storeValidate(Request $request, Image $except = null)
    {
        $nameUniqueValidateRule = Rule::unique("images", "name")->where("image_category_id", $request->image_category_id);
        if (!is_null($except)) {
            $nameUniqueValidateRule->ignore($except->id);
        }
        $this->validate($request, [
            "image_category_id" => "required|exists:image_categories,id",
            "name" => ["required", "max:32", $nameUniqueValidateRule],
            "min_disk" => "required|integer|min:0|max:" . Constants::UINT32_MAX,
            "min_memory" => "required|integer|min:0|max:" . Constants::UINT32_MAX,
            "machine_type" => "required|integer|min:0|max:1",
            "use_legacy_bios" => "required|integer|min:0|max:1",
            "internal_name" => "required|max:255",
            "force_version" => "nullable|max:255",
            "order" => "nullable|integer|min:-32768|max:32767",
            "status" => ["required", "integer", Rule::in(ImageStatusCode::AVAILABLE_STATUS_CODES)],
            "price_per_hour" => 'nullable|regex:/^(\d){0,4}(\.(\d){0,4}){0,1}$/',
        ], []);
    }

    private function retrieveValues(Request $request)
    {
        $values = [
            "image_category_id" => $request->image_category_id,
            "name" => $request->name,
            "min_disk" => $request->min_disk,
            "min_memory" => $request->min_memory,
            "machine_type" => $request->machine_type,
            "use_legacy_bios" => $request->use_legacy_bios,
            "internal_name" => $request->internal_name,
            "force_version" => $request->force_version,
            "order" => is_null($request->order) ? 0 : $request->order,
            "status" => $request->status,
            "price_per_hour" => is_null($request->price_per_hour) ? 0 : $request->price_per_hour,
        ];

        return $values;
    }
}
