<?php

namespace App\Http\Controllers\IPPool;

use App\ComputeInstance;
use App\ComputeInstance\OperationRequest;
use App\Constants\ComputeInstance\OperationRequest\TypeCode;
use App\Constants\GlobalErrorCode;
use App\Exceptions\CCMSAPIException;
use App\Exceptions\OperationRequestLimitException;
use App\Exceptions\QuotaExceededException;
use App\IPPool\Common;
use App\IPPool\IPv4;
use App\IPPool\IPv4Assignment;
use App\IPPool\IPv6;
use App\IPPool\IPv6Assignment;
use App\User;
use App\UserQuota;
use App\Utils\Node\ComputeNode\Exception\Exception;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use YunInternet\CCMSCommon\Constants\NetworkType;

class AssignmentController extends Controller
{
    /**
     * @param Request $request
     * @param IPv4Assignment|IPv6Assignment $ipAssignment
     * @throws CCMSAPIException
     */
    protected function addressAssignmentOwnerValidate(Request $request, $ipAssignment)
    {
        if ($request->user()->id !== $ipAssignment->user_id)
            throw new CCMSAPIException("UNAUTHORIZED_REQUEST", GlobalErrorCode::UNAUTHORIZED_REQUEST);
    }

    protected function addressAssignmentBindValidate(Request $request, $ipAssignment)
    {
        if ($ipAssignment->nic_id)
            throw new CCMSAPIException("Unbindable IP address", GlobalErrorCode::IP_ADDRESS_ALREADY_BIND);
    }

    protected function computeInstanceOwnerValidate(Request $request, ComputeInstance $computeInstance)
    {
        if ($computeInstance->user_id !== $request->user()->id)
            throw new CCMSAPIException("UNAUTHORIZED_REQUEST", GlobalErrorCode::UNAUTHORIZED_REQUEST);
    }

    /**
     * @param Request $request
     * @param IPv4Assignment|IPv6Assignment $ipAssignment
     * @throws CCMSAPIException
     */
    protected function addressAssignmentUnbindValidate(Request $request, $ipAssignment)
    {
        if (!$ipAssignment->unbindable)
            throw new CCMSAPIException("Unbindable IP address", GlobalErrorCode::IP_ADDRESS_UNBINDABLE);
    }

    /**
     * @param Request $request
     * @param ComputeInstance $computeInstance
     * @param $ipVersion
     * @return array
     * @throws CCMSAPIException
     */
    protected function doAllocatePublicForInstance(Request $request, ComputeInstance $computeInstance, $ipVersion)
    {
        $this->computeInstanceOwnerValidate($request, $computeInstance);
        $this->instanceOperableCheck($computeInstance);

        $nBlock = $request->allocateNum;
        if (!is_numeric($nBlock))
            $nBlock = 1;

        /**
         * @var User $user
         */
        $user = $request->user("web");
        $user->prepareForExclusiveOperation();

        try {
            $this->validateUserQuota($request->user("web"), $ipVersion, $nBlock);

            /**
             * @var ComputeInstance\Device\NetworkInterface $networkInterface
             */
            $networkInterface = $computeInstance->networkInterfaces()->where("type", NetworkType::TYPE_PUBLIC_NETWORK)->firstOrFail();
            try {
                Common::assignIPForNetworkInterface($ipVersion, $networkInterface, true, null, $nBlock);
            } catch (Exception $e) {
                return ["result" => false, "message" => $e->getMessage()];
            }
        } finally {
            $user->completeExclusiveOperation();
        }

        $this->updateIPAddress($networkInterface);
        return ["result" => true];
    }

    protected function doAllocatePublicForInstanceAsAdmin(Request $request, ComputeInstance $computeInstance, $ipVersion)
    {
        $this->instanceOperableCheck($computeInstance);

        $nBlock = $request->allocateNum;
        if (!is_numeric($nBlock))
            $nBlock = 1;

        /**
         * @var ComputeInstance\Device\NetworkInterface $networkInterface
         */
        $networkInterface = $computeInstance->networkInterfaces()->where("type", NetworkType::TYPE_PUBLIC_NETWORK)->firstOrFail();
        try {
            Common::assignIPForNetworkInterface($ipVersion, $networkInterface, true, null, $nBlock);
        } catch (Exception $e) {
            return ["result" => false, "message" => $e->getMessage()];
        }

        $this->updateIPAddress($networkInterface);
        return ["result" => true];
    }

    protected function doMassUnbind(Request $request, Builder $builder)
    {
        $items = $request->items;

        $networkInterfaceIdList = $this->whereForCurrentUserUnbindable($request, $builder->newQuery())
            ->whereIn("id", $items)
            ->groupBy("nic_id")
            ->pluck("nic_id")
            ->toArray()
        ;

        $count = $this->whereForCurrentUserUnbindable($request, $builder->newQuery())
            ->whereIn("id", $items)
            ->update([
                "nic_id" => null,
            ])
        ;

        $this->updateIPAddressByNetworkInterfaceIdList($networkInterfaceIdList);

        return ["result" => true, "count" => $count];
    }

    protected function doMassRelease(Request $request, Model $model)
    {
        $items = $request->items;

        $networkInterfaceIdList = $this->whereForCurrentUserUnbindable($request, $model::query())
            ->whereIn("id", $items)
            ->groupBy("nic_id")
            ->pluck("nic_id")
            ->toArray()
        ;

        $assignmentCollection = $this->whereForCurrentUserUnbindable($request, $model::query())
            ->whereIn("id", $items)
            ->get()
        ;

        $model::massCharge($assignmentCollection);

        $count = $this->whereForCurrentUserUnbindable($request, $model::query())
            ->whereIn("id", $items)
            ->update([
                "user_id" => null,
                "nic_id" => null,
            ])
        ;

        $this->updateIPAddressByNetworkInterfaceIdList($networkInterfaceIdList);

        return ["result" => true, "count" => $count];
    }

    protected function whereForCurrentUserUnbindable(Request $request, $builder)
    {
        $builder
            ->where("user_id", $request->user()->id)
            ->where("unbindable", 1)
        ;
        return $builder;
    }

    /**
     * @param Request $request
     * @param IPv4Assignment|IPv6Assignment $ipAssignment
     * @return array
     */
    protected function getBindableInstances(Request $request, $ipAssignment)
    {
        return ["result" => true, "bindableInstances" => ComputeInstance::query()->where("user_id", $request->user()->id)->whereIn("compute_node_id", $this->getBindableNodeList($ipAssignment))->get(["id", "unique_id", "name"])];
    }

    protected function bindAssignment(Request $request, $ipAssignment, ComputeInstance $computeInstance)
    {
        // $this->addressAssignmentOwnerValidate($request, $ipAssignment);
        $this->addressAssignmentBindValidate($request, $ipAssignment);
        $this->computeInstanceOwnerValidate($request, $computeInstance);
        $this->instanceOperableCheck($computeInstance);

        $publicNetworkInterface = $computeInstance->networkInterfaces()->where("type", NetworkType::TYPE_PUBLIC_NETWORK)->firstOrFail();

        $ipAssignment->update([
            "nic_id" => $publicNetworkInterface->id,
        ]);

        $this->updateIPAddress($publicNetworkInterface);

        return ["result" => true];
    }

    /**
     * @param Request $request
     * @param $ipAssignment
     * @return mixed
     * @throws CCMSAPIException
     */
    protected function unbindAssignment(Request $request, $ipAssignment)
    {
        // $this->addressAssignmentOwnerValidate($request, $ipAssignment);
        $this->addressAssignmentUnbindValidate($request, $ipAssignment);

        return $this->doUnbindAssignment($request, $ipAssignment);
    }

    /**
     * @param Request $request
     * @param $ipAssignment
     * @return mixed
     */
    protected function doUnbindAssignment(Request $request, $ipAssignment)
    {
        $networkInterface = $ipAssignment->networkInterface;
        $this->instanceOperableCheck($networkInterface->instance);

        $ipAssignment->update([
            "nic_id" => null,
            "unbindable" => 1,
        ]);
        $this->updateIPAddress($networkInterface);

        return ["result" => true];
    }

    protected function unbindAddress(Request $request, Builder $builder, $poolId, $position)
    {
        $ipAddress = $this->getIPAddress($builder, $poolId, $position);

        $this->addressAssignmentOwnerValidate($request, $ipAddress);
        $this->addressAssignmentUnbindValidate($request, $ipAddress);

        $networkInterface = $ipAddress->networkInterface;
        $this->instanceOperableCheck($networkInterface->instance);

        if ($ipAddress->unbindable) {
            $ipAddress->update([
                "nic_id" => null
            ]);
            $this->updateIPAddress($networkInterface);
            return ["result" => true];
        }

        return ["result" => false];
    }

    /**
     * @param Request $request
     * @param $ipAssignment
     * @return mixed
     * @throws CCMSAPIException
     */
    protected function releaseAssignment(Request $request, $ipAssignment)
    {
        // $this->addressAssignmentOwnerValidate($request, $ipAssignment);
        $this->addressAssignmentUnbindValidate($request, $ipAssignment);

        $this->doReleaseAssignment($request, $ipAssignment);

        return ["result" => true];
    }

    /**
     * @param Request $request
     * @param IPv4Assignment|IPv6Assignment $ipAssignment
     * @return mixed
     */
    protected function doReleaseAssignment(Request $request, $ipAssignment)
    {
        /**
         * @var ComputeInstance\Device\NetworkInterface $networkInterface
         */
        $networkInterface = $ipAssignment->networkInterface;
        $this->instanceOperableCheck($networkInterface->instance);

        if ($ipAssignment->unbindable) {
            $ipAssignment::massCharge($ipAssignment);
        }

        $ipAssignment->update([
            "user_id" => null,
            "nic_id" => null,
        ]);
        $this->updateIPAddress($networkInterface);

        return ["result" => true];
    }

    protected function releaseAddress(Request $request, Builder $builder, $poolId, $position)
    {
        $ipAddress = $this->getIPAddress($builder, $poolId, $position);

        $this->addressAssignmentOwnerValidate($request, $ipAddress);
        $this->addressAssignmentUnbindValidate($request, $ipAddress);

        $networkInterface = $ipAddress->networkInterface;
        $this->instanceOperableCheck($networkInterface->instance);
        if ($ipAddress->unbindable) {
            $ipAddress::massCharge($ipAddress);
            $ipAddress->update([
                "user_id" => null,
                "nic_id" => null,
            ]);
            $this->updateIPAddress($networkInterface);
            return ["result" => true];
        }
        return ["result" => false];
    }

    /**
     * @param Builder $builder
     * @param $poolId
     * @param $position
     * @return IPv4Assignment|IPv6Assignment
     */
    protected function getIPAddress(Builder $builder, $poolId, $position)
    {
        return $builder->where([
            "pool_id" => $poolId,
            "position" => $position,
        ])->firstOrFail();
    }

    /**
     * @param IPv4Assignment|IPv6Assignment $ipAssignment
     * @return int[]
     */
    protected function getBindableNodeList($ipAssignment)
    {
        /**
         * @var IPv4|IPv6 $pool
         */
        $pool = $ipAssignment->pool;
        return $pool->getBindableNodeList();
    }

    /**
     * @param IPv4Assignment|IPv6Assignment $ipAssignment
     * @return int[]
     */
    protected function doConvert($ipAssignment)
    {
        $ipAssignment->update([
            "unbindable" => !$ipAssignment->unbindable,
            "last_charged_at" => date("Y-m-d H:i:s"),
        ]);

        return $ipAssignment->unbindable;
    }

    protected function validateUserQuota(User $user, $version, $requirement)
    {
        /**
         * @var UserQuota $userQuota
         */
        $userQuota = $user->userQuota;
        $columnName = "max_elastic_ipv". $version ."_block";
        $functionName = "ipv" . $version . "s";
        if (is_null($userQuota->{$columnName}))
            return;
        $availableIPBlock = $userQuota->{$columnName} - $user->{$functionName}()->where("unbindable", "!=", 0)->count() - $requirement;

        if ($availableIPBlock < 0)
            throw new QuotaExceededException();
    }

    private function updateIPAddressByNetworkInterfaceIdList($ids)
    {
        if (is_integer($ids))
            $ids = [$ids];
        foreach (ComputeInstance\Device\NetworkInterface::query()->whereIn("id", $ids)->get() as $networkInterface) {
            try {
                $this->updateIPAddress($networkInterface);
            } catch (QueryException $e) {
            }
        }
    }

    private function instanceOperableCheck(ComputeInstance $computeInstance)
    {
        if ($computeInstance->processingOperationRequests()->count()) {
            throw new OperationRequestLimitException();
        }
    }

    private function updateIPAddress($networkInterface)
    {
        if (!$networkInterface)
            return;
        $computeInstance = $networkInterface->instance;
        OperationRequest::newRequestThenDispatch($computeInstance, TypeCode::TYPE_UPDATE_IP_ADDRESS, $networkInterface->mac_address);
    }
}
