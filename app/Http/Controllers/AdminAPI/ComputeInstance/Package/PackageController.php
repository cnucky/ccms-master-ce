<?php

namespace App\Http\Controllers\AdminAPI\ComputeInstance\Package;

use App\ComputeInstance;
use App\ComputeInstancePackage;
use App\ComputeInstancePackageCategory;
use App\Constants\Constants;
use App\Constants\GlobalErrorCode;
use App\Constants\ImageStatusCode;
use App\Zone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use YunInternet\CCMSCommon\CommonUtils;

class PackageController extends Controller
{
    const UINT32_MAX = 4294967295;
    const UINT32_VALIDATE_RULE = "nullable|integer|min:0|max:" . self::UINT32_MAX;

    public function index()
    {
        return [
            "result" => true,
            "packageCategories" => ComputeInstancePackageCategory::query()
                ->with(["packages" => function ($builder) {
                    $builder->withCount("instances");
                }])
                ->get()
        ];
    }

    public function store(Request $request)
    {
        $this->storeValidate($request);

        $package = ComputeInstancePackage::query()->create($this->retrieveValues($request));

        return ["result" => true, "package" => $package];
    }

    public function update(Request $request, ComputeInstancePackage $computeInstancePackage)
    {
        $this->storeValidate($request, $computeInstancePackage);

        $computeInstancePackage->update($this->retrieveValues($request));

        return ["result" => true, "package" => $computeInstancePackage];
    }

    public function destroy(ComputeInstancePackage $computeInstancePackage)
    {
        if ($computeInstancePackage->instances()->count())
            return ["result" => false, "errno" => GlobalErrorCode::PACKAGE_IS_BEING_USED_BY_INSTANCES];
        ComputeInstance::onlyTrashed()->where("compute_instance_package_id", $computeInstancePackage->id)->update([
            "compute_instance_package_id" => null,
        ]);
        $computeInstancePackage->delete();
        return ["result" => true];
    }

    private function storeValidate(Request $request, ComputeInstancePackage $except = null)
    {
        $nameUniqueValidateRule = Rule::unique("compute_instance_packages", "name")->where("category_id", $request->category_id);
        if (!is_null($except)) {
            $nameUniqueValidateRule->ignore($except->id);
        }
        $this->validate($request, [
            "name" => ["required", "max:32", $nameUniqueValidateRule],
            "category_id" => "required|exists:compute_instance_package_categories,id",
            "vCPU" => "required|integer|min:1",
            "memory" => "required|integer|min:1",
            "public_ipv4" => self::UINT32_VALIDATE_RULE,
            "public_ipv4_block_size" => "nullable|integer|min:0|max:32",
            "public_ipv6" => self::UINT32_VALIDATE_RULE,
            "public_ipv6_block_size" => "nullable|integer|min:0|max:128",
            "traffic" => self::UINT32_VALIDATE_RULE,
            "inbound_traffic" => self::UINT32_VALIDATE_RULE,
            "outbound_traffic" => self::UINT32_VALIDATE_RULE,
            "inbound_bandwidth" => self::UINT32_VALIDATE_RULE,
            "outbound_bandwidth" => self::UINT32_VALIDATE_RULE,
            "io_weight" => "nullable|integer|min:10|max:1000",
            "read_bytes_sec" => self::UINT32_VALIDATE_RULE,
            "write_bytes_sec" => self::UINT32_VALIDATE_RULE,
            "read_iops_sec" => self::UINT32_VALIDATE_RULE,
            "write_iops_sec" => self::UINT32_VALIDATE_RULE,
            "order" => "nullable|integer|min:-32768|max:32767",
            "status" => ["required", Rule::in(ImageStatusCode::AVAILABLE_STATUS_CODES)],
            "price_per_hour" => Constants::DECIMAL_8_4_PRICE_VALIDATE_RULE,
        ]);
    }

    private function retrieveValues(Request $request)
    {
        return [
            "name" => $request->name,
            "category_id" => $request->category_id,
            "vCPU" => $request->vCPU,
            "memory" => $request->memory,
            "public_ipv4" => CommonUtils::fallbackValueOnNullValue($request->public_ipv4, 0),
            "public_ipv4_block_size" => $request->public_ipv4_block_size,
            "public_ipv6" => CommonUtils::fallbackValueOnNullValue($request->public_ipv6, 0),
            "public_ipv6_block_size" => $request->public_ipv6_block_size,
            "traffic" => $request->traffic,
            "inbound_traffic" => $request->inbound_traffic,
            "outbound_traffic" => $request->outbound_traffic,
            "inbound_bandwidth" => CommonUtils::fallbackValueOnNullValue($request->inbound_bandwidth, 0),
            "outbound_bandwidth" => CommonUtils::fallbackValueOnNullValue($request->outbound_bandwidth, 0),
            "io_weight" => $request->io_weight,
            "read_bytes_sec" => CommonUtils::fallbackValueOnNullValue($request->read_bytes_sec, 0),
            "write_bytes_sec" => CommonUtils::fallbackValueOnNullValue($request->write_bytes_sec, 0),
            "read_iops_sec" => CommonUtils::fallbackValueOnNullValue($request->read_iops_sec, 0),
            "write_iops_sec" => CommonUtils::fallbackValueOnNullValue($request->write_iops_sec, 0),
            "order" => CommonUtils::fallbackValueOnNullValue($request->order, 0),
            "status" => $request->status,
            "price_per_hour" => CommonUtils::fallbackValueOnNullValue($request->price_per_hour, 0),
        ];
    }
}
