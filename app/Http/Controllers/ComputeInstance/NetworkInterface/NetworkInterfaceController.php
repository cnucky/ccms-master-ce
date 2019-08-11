<?php

namespace App\Http\Controllers\ComputeInstance\NetworkInterface;

use App\ComputeInstance;
use App\ComputeResourceOperationRequest;
use App\Constants\ComputeInstance\OperationRequest\TypeCode;
use App\Constants\ComputeInstanceStatusCode;
use App\Constants\ComputeResourceOperation\ResourceTypeCode;
use App\Constants\GlobalErrorCode;
use App\Exceptions\CCMSAPIException;
use App\IPPool\IPv4Assignment;
use App\IPPool\IPv6Assignment;
use App\Utils\ComputeInstance\OperationRequestLimitChecker;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class NetworkInterfaceController extends Controller
{
    public function index(ComputeInstance $computeInstance)
    {
        return ["result" => true, "networkInterfaces" => $computeInstance->networkInterfaces()->with([
            "ipv4Addresses",
            "ipv6Addresses",
            "ipv4Addresses.pool:id,subnet_network_bits,network_bits,human_readable_gateway",
            "ipv6Addresses.pool:id,subnet_network_bits,network_bits,human_readable_gateway",
        ])->whereIn("type", [0, 1])->get()];
    }

    public function changeModel(Request $request, ComputeInstance\Device\NetworkInterface $networkInterface)
    {
        $checkResult = OperationRequestLimitChecker::check($networkInterface->instance);
        if ($networkInterface->type !== 0 && $networkInterface->type !== 1)
            return ["result" => false, "message" => "Invalid network interface"];

        if ($networkInterface->instance->power_status !== ComputeInstanceStatusCode::POWER_STATUS_OFF)
            throw new CCMSAPIException("Power off instance is required", GlobalErrorCode::POWER_OFF_INSTANCE_IS_REQUIRED);

        if ($checkResult !== true)
            return $checkResult;

        $this->validate($request, [
            "model" => [
                "required",
                Rule::in([0, 1, 2])
            ],
        ]);

        if ($request->saveAndApply) {
            $operationRequest = ComputeResourceOperationRequest::newOperationRequestThenDispatch($networkInterface->instance->user_id, ResourceTypeCode::TYPE_COMPUTE_INSTANCE, $networkInterface->instance->id, TypeCode::TYPE_CHANGE_NETWORK_INTERFACE_MODEL, [
                "networkInterfaceId" => $networkInterface->id,
                "model" => $request->model,
            ]);

            return ["result" => true, "operationRequest" => $operationRequest];
        } else {
            $networkInterface->update(["model" => $request->model]);
            return ["result" => true];
        }
    }
}
