<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-14
 * Time: 下午8:22
 */

namespace App\Http\Controllers\ComputeInstance;


use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

trait StoreValidator
{
    private $trans = [
        "name" => "名称",
        "hostname" => "主机名",
    ];

    public function meteDataValidate(Request $request)
    {
        $this->validate($request, [
            "name" => "required|max:28",
            "hostname" => [
                "required",
                "regex:/^[a-zA-Z\-\.\d]{2,64}$/",
            ],
            "description" => "nullable|max:255",
            "count" => "required|integer|min:1",
            "selectedZoneId" => [
                "required",
                Rule::exists("zones", "id"),
            ],
        ], [], [""]);
    }

    public function areaValidate(Request $request)
    {
        $this->validate($request, [
            "selectedRegionId" => "required|exists:regions,id",
            "selectedZoneId" => "nullable|exists:zones,id",
        ]);
    }

    public function imageValidate(Request $request)
    {
        $this->validate($request, [
            "selectedImageId" => "nullable|exists:images,id",
        ]);
    }
}