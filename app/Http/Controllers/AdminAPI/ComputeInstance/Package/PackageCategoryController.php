<?php

namespace App\Http\Controllers\AdminAPI\ComputeInstance\Package;

use App\ComputeInstancePackageCategory;
use App\Constants\GlobalErrorCode;
use App\Constants\ImageStatusCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class PackageCategoryController extends Controller
{
    public function store(Request $request)
    {
        $this->storeValidate($request);

        $category = ComputeInstancePackageCategory::query()->create($this->retrieveValues($request));

        return ["result" => true, "category" => $category];
    }

    public function update(Request $request, ComputeInstancePackageCategory $computeInstancePackageCategory)
    {
        $this->storeValidate($request, $computeInstancePackageCategory);

        $computeInstancePackageCategory->update($this->retrieveValues($request));

        return ["result" => true, "category" => $computeInstancePackageCategory];
    }

    public function destroy(ComputeInstancePackageCategory $computeInstancePackageCategory)
    {
        if ($computeInstancePackageCategory->instances()->count())
            return ["result" => false, "errno" => GlobalErrorCode::PACKAGE_IS_BEING_USED_BY_INSTANCES];
        $computeInstancePackageCategory->delete();
        return ["result" => true];
    }

    private function storeValidate(Request $request, ComputeInstancePackageCategory $except = null)
    {
        $nameUniqueValidateRule = Rule::unique("compute_instance_package_categories", "name");
        if (!is_null($except)) {
            $nameUniqueValidateRule->ignore($except->id);
        }

        $this->validate($request, [
            "name" => ["required", "max:20", $nameUniqueValidateRule],
            "status" => ["required", Rule::in(ImageStatusCode::AVAILABLE_STATUS_CODES)]
        ]);
    }

    private function retrieveValues(Request $request)
    {
        return [
            "name" => $request->name,
            "status" => $request->status,
        ];
    }
}
