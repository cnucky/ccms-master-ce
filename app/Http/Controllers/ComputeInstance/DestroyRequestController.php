<?php

namespace App\Http\Controllers\ComputeInstance;

use App\ComputeInstance;
use App\Constants\ComputeInstance\OperationRequest\StatusCode;
use App\Constants\ComputeInstance\OperationRequest\TypeCode;
use App\Constants\ComputeInstanceStatusCode;
use App\Constants\GlobalErrorCode;
use App\Exceptions\IsBeingCharged;
use App\IPPool\IPv4Assignment;
use App\IPPool\IPv6Assignment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use YunInternet\CCMSCommon\Constants\NetworkType;

class DestroyRequestController extends Controller
{
    public function destroy(Request $request, ComputeInstance $computeInstance)
    {
        if ($computeInstance->status !== ComputeInstanceStatusCode::STATUS_CREATE_UNSUCCESSFULLY) {
            ComputeInstance::massCharge($computeInstance);
            if ($request->deleteAttachedVolumes) {
                ComputeInstance\LocalVolume::massCharge($computeInstance->attachedLocalVolumes);
            }
            if ($request->releaseExtraIPs) {
                IPv4Assignment::massCharge($computeInstance->ipv4s()->where("unbindable", "!=", 0)->get());
                IPv6Assignment::massCharge($computeInstance->ipv6s()->where("unbindable", "!=", 0)->get());
            }
        }

        $operationRequest = ComputeInstance\OperationRequest::newRequestThenDispatch($computeInstance, TypeCode::TYPE_DESTROY, [
            "deleteAttachedVolumes" => boolval($request->deleteAttachedVolumes),
            "releaseExtraIPs" => boolval($request->releaseExtraIPs),
            "uniqueId" => $computeInstance->unique_id,
            "name" => $computeInstance->name,
        ]);

        return ["result" => true, "operationRequest" => $operationRequest];
    }

    public function massDestroy(Request $request)
    {
        /**
         * @var Collection $computeInstanceModelList
         */
        $computeInstanceModelList = self::retrieveDestroyableInstances($request)
            ->where(ComputeInstanceControllerUtils::whereCurrentUserInstancesClosure($request))
            ->get()
        ;

        return $this->doMassDestroy($request, $computeInstanceModelList);
    }

    public function massDestroyForAdmin(Request $request)
    {
        $computeInstanceModelList = self::retrieveDestroyableInstances($request)
            ->get()
        ;

        return $this->doMassDestroy($request, $computeInstanceModelList);
    }

    /**
     * @param Request $request
     * @param Collection $computeInstanceModelList
     * @return array
     */
    private function doMassDestroy(Request $request, $computeInstanceModelList)
    {
        ComputeInstance::massCharge($computeInstanceModelList);
        $computeInstanceIdList = $computeInstanceModelList->pluck("id");
        if ($request->deleteAttachedVolumes) {
            ComputeInstance\LocalVolume::massCharge(ComputeInstance\LocalVolume::query()->whereIn("attached_compute_instance_id", $computeInstanceIdList)->get());
        }
        if ($request->releaseExtraIPs) {
            $publicNetworkInterfaceIdList = ComputeInstance\Device\NetworkInterface::query()->whereIn("compute_instance_id", $computeInstanceIdList)->where("type", NetworkType::TYPE_PUBLIC_NETWORK)->pluck("id");
            IPv4Assignment::massCharge(IPv4Assignment::query()->whereIn("nic_id", $publicNetworkInterfaceIdList)->where("unbindable", "!=", 0)->get());
            IPv6Assignment::massCharge(IPv6Assignment::query()->whereIn("nic_id", $publicNetworkInterfaceIdList)->where("unbindable", "!=", 0)->get());
        }

            $operationRequests = [];
        foreach ($computeInstanceModelList as $computeInstance) {
            try {
                $operationRequests[] = ComputeInstance\OperationRequest::newRequestThenDispatch($computeInstance, TypeCode::TYPE_DESTROY, [
                    "deleteAttachedVolumes" => boolval($request->deleteAttachedVolumes),
                    "releaseExtraIPs" => boolval($request->releaseExtraIPs),
                    "uniqueId" => $computeInstance->unique_id,
                    "name" => $computeInstance->name,
                ]);
            } catch (QueryException $e) {
            }
        }

        return ["result" => true, "operationRequests" => $operationRequests];
    }

    private static function retrieveDestroyableInstances(Request $request)
    {
        return ComputeInstance::query()->whereIn("id", $request->items)
            ->whereDoesntHave("processingOperationRequests")
            ->whereIn("status", [ComputeInstanceStatusCode::STATUS_NORMAL, ComputeInstanceStatusCode::STATUS_CREATE_UNSUCCESSFULLY, ComputeInstanceStatusCode::STATUS_DESTROY_UNSUCCESSFULLY])
            ;
    }
}
