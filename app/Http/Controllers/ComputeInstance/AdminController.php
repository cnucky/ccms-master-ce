<?php

namespace App\Http\Controllers\ComputeInstance;

use App\ComputeInstance;
use App\Constants\ComputeInstanceStatusCode;
use App\Constants\Constants;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    const UINT32_MAX = 4294967295;
    const UINT32_VALIDATE_RULE = "nullable|integer|min:0|max:" . self::UINT32_MAX;
    const INT32_VALIDATE_RULE = "nullable|integer|min:-2147483648|max:2147483647";

    public function changeCustomSize(Request $request, ComputeInstance $computeInstance)
    {
        $this->validate($request, [
            "override_vCPU" => "nullable|integer|min:1",
            "override_memory" => "nullable|integer|min:1",
            "override_traffic" => self::INT32_VALIDATE_RULE,
            "override_inbound_traffic" => self::INT32_VALIDATE_RULE,
            "override_outbound_traffic" => self::INT32_VALIDATE_RULE,
            "override_inbound_bandwidth" => self::UINT32_VALIDATE_RULE,
            "override_outbound_bandwidth" => self::UINT32_VALIDATE_RULE,
            "override_io_weight" => "nullable|integer|min:10|max:1000",
            "override_read_bytes_sec" => self::UINT32_VALIDATE_RULE,
            "override_write_bytes_sec" => self::UINT32_VALIDATE_RULE,
            "override_read_iops_sec" => self::UINT32_VALIDATE_RULE,
            "override_write_iops_sec" => self::UINT32_VALIDATE_RULE,
            "override_price_per_hour" => Constants::DECIMAL_8_4_PRICE_VALIDATE_RULE,
        ]);

        $computeInstance->update([
            "override_vCPU" => $request->override_vCPU,
            "override_memory" => $request->override_memory,
            "override_traffic" => $request->override_traffic,
            "override_inbound_traffic" => $request->override_inbound_traffic,
            "override_outbound_traffic" => $request->override_outbound_traffic,
            "override_inbound_bandwidth" => $request->override_inbound_bandwidth,
            "override_outbound_bandwidth" => $request->override_outbound_bandwidth,
            "override_io_weight" => $request->override_io_weight,
            "override_read_bytes_sec" => $request->override_read_bytes_sec,
            "override_write_bytes_sec" => $request->override_write_bytes_sec,
            "override_read_iops_sec" => $request->override_read_iops_sec,
            "override_write_iops_sec" => $request->override_write_iops_sec,
            "override_price_per_hour" => $request->override_price_per_hour,
        ]);

        return ["result" => true];
    }

    public function changeAdvanceSettings(Request $request, ComputeInstance $computeInstance)
    {
        $this->validate($request, [
            "statis" => Rule::in(ComputeInstanceStatusCode::AVAILABLE_STATUS),
        ]);

        $computeInstance->update([
            "no_clean_traffic" => boolval($request->no_clean_traffic),
            "status" => $request->status,
        ]);

        return ["result" => true];
    }

    public function forceDelete(Request $request, ComputeInstance $computeInstance)
    {
        DB::transaction(function () use ($request, $computeInstance) {
            if ($request->deleteAttachedVolumes) {
                foreach ($computeInstance->attachedLocalVolumes as $attachedLocalVolume) {
                    $attachedLocalVolume->deleteWithNodeCounterUpdate();
                }
            } else {
                $computeInstance->attachedLocalVolumes()->update(["attached_compute_instance_id" => null]);
            }

            /**
             * @var ComputeInstance\Device\NetworkInterface $networkInterface
             */
            if ($request->releaseExtraIPs) {
                foreach ($computeInstance->networkInterfaces as $networkInterface) {
                    $networkInterface->releaseIPAddresses();
                }
            } else {
                foreach ($computeInstance->networkInterfaces as $networkInterface) {
                    $networkInterface->unbindIPAddresses();
                    // Release unbindable addresses
                    $networkInterface->releaseIPAddresses(function ($query) {
                        $query->where("unbindable", 0);
                    });
                }
            }

            $computeInstance->deleteWithNodeCounterUpdate();
        });

        return ["result" => true];
    }
}
