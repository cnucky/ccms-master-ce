<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-5
 * Time: 上午1:00
 */

namespace App\Http\Controllers\AdminAPI;


use App\Constants\CommonStatusCode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

trait ImageCommonCategoryValidator
{
    private function storeValidate(Request $request, $uniqueTable, $except = null)
    {
        $nameUniqueValidateRule = Rule::unique($uniqueTable, "name");
        if (!is_null($except)) {
            $nameUniqueValidateRule->ignore($except->id);
        }
        $this->validate($request, [
            "name" => ["required", "max:32", $nameUniqueValidateRule],
            "icon_class" => "nullable|max:16",
            "order" => "nullable|integer|min:-32768|max:32767",
            "status" => ["required", Rule::in(CommonStatusCode::AVAILABLE_STATUS_CODES)]
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