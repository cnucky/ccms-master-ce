<?php

namespace App\Http\Controllers\LocalVolume;

use App\ComputeInstance;
use App\ComputeInstance\LocalVolume;
use App\ComputeResourceOperationRequest;
use App\Constants\ComputeInstanceStatusCode;
use App\Constants\ComputeResourceOperation\ResourceTypeCode;
use App\Constants\GlobalErrorCode;
use App\Constants\Volume\OperationRequest\TypeCode;
use App\Constants\Volume\StatusCode;
use App\Exceptions\CCMSAPIException;
use App\Exceptions\IsBeingCharged;
use App\Exceptions\MinCreditRequired;
use App\Exceptions\QuotaExceededException;
use App\Jobs\Volume\NewLocalVolume;
use App\Node\ComputeNode;
use App\User;
use App\Utils\ComputeInstance\ComputeInstances;
use App\Utils\ComputeInstance\ComputeInstanceUtils;
use App\Utils\ComputeInstance\OperationRequestLimitChecker;
use App\Zone;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use YunInternet\CCMSCommon\Constants\Constants;
use YunInternet\CCMSCommon\Constants\MachineType;
use YunInternet\CCMSCommon\Constants\VolumeBusCode;

class LocalVolumeController extends Controller
{
    use \App\Http\Controllers\LocalVolume\Controller;

    public function resize(Request $request, LocalVolume $localVolume)
    {
        $this->validate($request, [
            "newCapacity" => "required|integer|min:" . ($localVolume->capacity + 1) . "|max:" . Constants::UINT32_MAX,
        ]);

        $originCapacity = $localVolume->capacity;
        $newCapacity = intval($request->newCapacity);
        $diffCapacity = $newCapacity - $originCapacity;

        /**
         * @var User $user
         */
        $user = $request->user();
        $user->prepareForExclusiveOperation();
        $this->validateUserQuota($user, $diffCapacity);

        try {
            LocalVolume::massCharge($localVolume);

            $this->attachedInstancePowerStatusValidate($localVolume);

            /**
             * @var ComputeNode $computeNode
             */
            $computeNode = $localVolume->node;

            $computeNode->allocateDiskCapacity($diffCapacity, function () use ($localVolume, $originCapacity, $newCapacity, $diffCapacity, &$operationRequest) {
                $localVolume->update([
                    "capacity" => $newCapacity,
                ]);

                $operationRequest = ComputeResourceOperationRequest::newOperationRequestThenDispatch($localVolume->user_id, ResourceTypeCode::TYPE_LOCAL_VOLUME, $localVolume->id, TypeCode::TYPE_RESIZE, [
                    "originCapacity" => $originCapacity,
                    "newCapacity" => $newCapacity,
                    "diffCapacity" => $diffCapacity,
                ]);
            });
        } finally {
            $user->completeExclusiveOperation();
        }

        return ["result" => true, "operationRequest" => $operationRequest];
    }

    public function changeBus(Request $request, LocalVolume $localVolume)
    {
        $this->validate($request, [
            "bus" => [
                "required",
                Rule::in([
                    VolumeBusCode::BUS_IDE,
                    VolumeBusCode::BUS_SATA,
                    VolumeBusCode::BUS_SCSI,
                    VolumeBusCode::BUS_VIRTIO,
                ])
            ],
        ]);

        $instance = $localVolume->instance;

        $checkResult = OperationRequestLimitChecker::check($instance);
        if ($checkResult !== true)
            return $checkResult;

        if ($instance->machine_type === MachineType::TYPE_Q35 && $request->bus == VolumeBusCode::BUS_IDE)
            return ["result" => false, "errno" => GlobalErrorCode::Q35_NOT_SUPPORT_IDE];


        $localVolume->update([
            "bus" => $request->bus,
        ]);

        if ($request->saveAndApply) {
            $this->attachedInstancePowerStatusValidate($localVolume);
            $operationRequest = ComputeResourceOperationRequest::newOperationRequestThenDispatch($localVolume->user_id, ResourceTypeCode::TYPE_COMPUTE_INSTANCE, $localVolume->instance->id, \App\Constants\ComputeInstance\OperationRequest\TypeCode::TYPE_RECONFIGURE);
            return ["result" => true, "operationRequest" => $operationRequest];
        } else {
            return ["result" => true];
        }
    }

    public function newVolume(Request $request, ComputeInstance $computeInstance)
    {
        $this->validate($request, [
            "capacity" => "required|min:1|max:" . Constants::UINT32_MAX,
            "bus" => [
                "required",
                Rule::in(array_keys(VolumeBusCode::BUS_CODE_2_TEXT))
            ],
        ]);

        if ($computeInstance->machine_type === MachineType::TYPE_Q35 && $request->bus == VolumeBusCode::BUS_IDE)
            return ["result" => false, "errno" => GlobalErrorCode::Q35_NOT_SUPPORT_IDE];

        /**
         * @var User $user
         */
        $user = $request->user();
        $user->prepareForExclusiveOperation();
        $this->validateUserQuota($user, $request->capacity);

        try {
            /**
             * @var ComputeNode $computeNode
             */
            $computeNode = $computeInstance->node;

            $computeNode->allocateDiskCapacity($request->capacity, function () use ($request, $computeInstance, &$operationRequest, $computeNode) {
                $localVolume = LocalVolume::query()->create([
                    "user_id" => $request->user()->id,
                    "unique_id" => "ulv-" . ComputeInstances::generateUniqueId($computeInstance->id . $computeInstance->user->id),
                    "compute_node_id" => $computeInstance->compute_node_id,
                    "capacity" => $request->capacity,
                    "bus" => $request->bus,
                    "allow_detach" => 1,
                    "attached_compute_instance_id" => $computeInstance->id,
                    "status" => StatusCode::STATUS_CREATING,
                ]);
                $computeNode->increaseLocalVolumeCounter();
                $operationRequest = ComputeResourceOperationRequest::newOperationRequestThenDispatch($localVolume->user_id, ResourceTypeCode::TYPE_LOCAL_VOLUME, $localVolume->id, TypeCode::TYPE_NEW);
            });
        } finally {
            $user->completeExclusiveOperation();
        }

        return ["result" => true, "operationRequest" => $operationRequest];
    }

    public function attach(Request $request, LocalVolume $localVolume, ComputeInstance $computeInstance)
    {
        if ($localVolume->compute_node_id !== $computeInstance->compute_node_id)
            return ["result" => false, "errno" => GlobalErrorCode::VOLUME_CAN_NOT_ATTACH_TO_TARGET_INSTANCE];
        if ($localVolume->instance)
            return ["result" => false, "errno" => GlobalErrorCode::VOLUME_ALREADY_ATTACH_TO_AN_INSTANCE];

        $this->validate($request, [
            "bus" => [
                "required",
                Rule::in([
                    VolumeBusCode::BUS_IDE,
                    VolumeBusCode::BUS_SATA,
                    VolumeBusCode::BUS_SCSI,
                    VolumeBusCode::BUS_VIRTIO,
                ])
            ],
        ]);

        return ["result" => true, "operationRequest" => ComputeResourceOperationRequest::newOperationRequestThenDispatch($localVolume->user_id, ResourceTypeCode::TYPE_LOCAL_VOLUME, $localVolume->id, TypeCode::TYPE_ATTACH, ["attach2Instance" => $computeInstance->id, "bus" => $request->bus])];
    }

    public function detach(LocalVolume $localVolume)
    {
        if (!$localVolume->attached_compute_instance_id)
            return ["result" => false];
        if ($localVolume->protected)
            return ["result" => false, GlobalErrorCode::IN_PROTECTED_MODE];
        return ["result" => true, "operationRequest" => ComputeResourceOperationRequest::newOperationRequestThenDispatch($localVolume->user_id, ResourceTypeCode::TYPE_LOCAL_VOLUME, $localVolume->id, TypeCode::TYPE_DETACH)];
    }

    public function massDetach(Request $request)
    {
        $localVolumeModelList = LocalVolume::query()
            ->whereIn("id", $request->items)
            ->where(self::whereBelong2UserClosure($request))
            ->where(self::whereNotProtectedClosure())
            ->get();

        return $this->doMassDetach($localVolumeModelList);
    }

    public function massDetachForAdmin(Request $request)
    {
        $localVolumeModelList = LocalVolume::query()
            ->whereIn("id", $request->items)
            ->where(self::whereNotProtectedClosure())
            ->get();

        return $this->doMassDetach($localVolumeModelList);
    }

    public function release(LocalVolume $localVolume)
    {
        LocalVolume::massCharge($localVolume);
        if ($localVolume->protected)
            return ["result" => false, GlobalErrorCode::IN_PROTECTED_MODE];
        return ["result" => true, "operationRequest" => ComputeResourceOperationRequest::newOperationRequestThenDispatch($localVolume->user_id, ResourceTypeCode::TYPE_LOCAL_VOLUME, $localVolume->id, TypeCode::TYPE_RELEASE, $localVolume->unique_id)];
    }

    public function massRelease(Request $request)
    {
        $localVolumeModelList = LocalVolume::query()
            ->whereIn("id", $request->items)
            ->where(self::whereBelong2UserClosure($request))
            ->where(self::whereNotProtectedClosure())
            ->get()
        ;

        return $this->doMassRelease($localVolumeModelList);
    }

    public function massReleaseForAdmin(Request $request)
    {
        $localVolumeModelList = LocalVolume::query()
            ->whereIn("id", $request->items)
            ->where(self::whereNotProtectedClosure())
            ->get()
        ;

        return $this->doMassRelease($localVolumeModelList);
    }

    public function attachedInstancePowerStatusValidate(LocalVolume $localVolume)
    {
        if ($localVolume->instance && $localVolume->instance->power_status !== ComputeInstanceStatusCode::POWER_STATUS_OFF)
            throw new CCMSAPIException("Power off instance is required", GlobalErrorCode::POWER_OFF_INSTANCE_IS_REQUIRED);
    }

    public function toggleProtectMode(LocalVolume $localVolume)
    {
        $localVolume->update([
            "protected" => !$localVolume->protected
        ]);
        return ["result" => true, "protected" => $localVolume->protected];
    }

    public function togglePrimaryBootableDisk(LocalVolume $localVolume)
    {
        /**
         * @var ComputeInstance $computeInstance
         */
        $computeInstance = $localVolume->instance;
        if ($computeInstance->power_status === ComputeInstanceStatusCode::POWER_STATUS_RUNNING)
            return ["result" => false, "errno" => GlobalErrorCode::POWER_OFF_INSTANCE_IS_REQUIRED];

        $computeInstance->attachedLocalVolumes()
            ->where("attach_order", 0)
            ->update([
            "attach_order" => 255
            ])
        ;

        $localVolume->update([
            "attach_order" => 0
        ]);

        $operationRequest = ComputeResourceOperationRequest::newOperationRequestThenDispatch($localVolume->user_id, ResourceTypeCode::TYPE_COMPUTE_INSTANCE, $localVolume->instance->id, \App\Constants\ComputeInstance\OperationRequest\TypeCode::TYPE_RECONFIGURE);
        return ["result" => true, "operationRequest" => $operationRequest];
    }

    /**
     * @param Collection $localVolumeModelList
     * @return array
     */
    private function doMassDetach($localVolumeModelList)
    {
        $operationRequests = [];
        foreach ($localVolumeModelList as $localVolume) {
            try {
                $operationRequests[] = ComputeResourceOperationRequest::newOperationRequestThenDispatch($localVolume->user_id, ResourceTypeCode::TYPE_LOCAL_VOLUME, $localVolume->id, TypeCode::TYPE_DETACH);
            } catch (QueryException $e) {
            }
        }

        return ["result" => true, "operationRequests" => $operationRequests];
    }

    /**
     * @param Collection $localVolumeCollection
     */
    private function doMassRelease($localVolumeCollection)
    {
        LocalVolume::massCharge($localVolumeCollection);
        $operationRequests = [];
        foreach ($localVolumeCollection as $localVolume) {
            try {
                $operationRequests[] = ComputeResourceOperationRequest::newOperationRequestThenDispatch($localVolume->user_id, ResourceTypeCode::TYPE_LOCAL_VOLUME, $localVolume->id, TypeCode::TYPE_RELEASE);
            } catch (QueryException $e) {
            }
        }

        return ["result" => true, "operationRequests" => $operationRequests];
    }

    private function validateUserQuota(User $user, int $capacityRequirement)
    {
        $userQuota = $user->userQuota;
        if (is_null($userQuota->max_storage_capacity_in_gib_unit))
            return;
        $availableDiskCapacity = $userQuota->max_storage_capacity_in_gib_unit - $user->localVolumes()->sum("capacity") - $capacityRequirement;

        if ($availableDiskCapacity < 0)
            throw new QuotaExceededException();
    }

    private function validateUserCredit(User $user, Zone $zone, int $totalCapacityRequirement)
    {
        $totalPricePerMonth = intval($zone->storage_price_per_hour_per_gib * $totalCapacityRequirement * 720);
        if ($user->credit < $totalCapacityRequirement) {
            throw new MinCreditRequired($totalCapacityRequirement, $user->credit);
        }
    }
}