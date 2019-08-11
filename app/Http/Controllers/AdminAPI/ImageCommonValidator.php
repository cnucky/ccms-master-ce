<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-5
 * Time: 上午1:42
 */

namespace App\Http\Controllers\AdminAPI;


use App\Constants\CommonStatusCode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Trait ImageCommonValidator
 * @property string $categoryTable
 * @package App\Http\Controllers\AdminAPI
 */
trait ImageCommonValidator
{
    protected static $trans = [
        "image_category_id" => "类别",
        "internal_name" => "内部名称",
        "force_version" => "强制版本",
        "order" => "顺序",
        "status" => "状态",
        "price_per_hour" => "每小时价格",
    ];

    private function storeValidate(Request $request, $tableName, $except = null)
    {
        $nameUniqueValidateRule = Rule::unique($tableName, "name")->where("category_id", $request->category_id);
        if (!is_null($except)) {
            $nameUniqueValidateRule->ignore($except->id);
        }
        $this->validate($request, [
            "category_id" => "required|exists:" . $this->categoryTable . ",id",
            "name" => ["required", "max:32", $nameUniqueValidateRule],
            "internal_name" => "required|max:255",
            "order" => "nullable|integer|min:-32768|max:32767",
            "status" => ["required", "integer", Rule::in(CommonStatusCode::AVAILABLE_STATUS_CODES)],
        ], [], self::$trans);
    }

    private function retrieveValues(Request $request)
    {
        $values = [
            "category_id" => $request->category_id,
            "name" => $request->name,
            "internal_name" => $request->internal_name,
            "order" => is_null($request->order) ? 0 : $request->order,
            "status" => $request->status,
        ];

        return $values;
    }
}