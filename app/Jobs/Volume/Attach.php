<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-25
 * Time: 下午7:02
 */

namespace App\Jobs\Volume;


use App\ComputeInstance;
use App\Constants\Volume\OperationRequest\SubStatus;
use App\Utils\Node\ComputeNode\Exception\Exception;
use YunInternet\CCMSCommon\Constants\ErrorSource;

class Attach extends OperationRequest
{
    protected function operationRequestHandle()
    {
        $data = $this->getJSONDecodedData();
        /**
         * @var ComputeInstance $computeInstance
         */
        try {
            $computeInstance = ComputeInstance::query()->findOrFail($data->attach2Instance);
            $computeInstance->createUtils()->attachVolume($this->getVolume()->unique_id, $data->bus);
            $this->getVolume()->update([
                "attached_compute_instance_id" => $data->attach2Instance,
                "bus" => $data->bus,
            ]);
        } catch (Exception $e) {
            if ($e->getResponse() && @$e->getResponse()->source === ErrorSource::SOURCE_LIBVIRT) {
                $subStatus = @SubStatus::LIBVIRT_ERRNO_MAP_2_SUB_STATUS[@$e->getResponse()->errno];
                if (is_null($subStatus))
                    throw $e;

                $this->getComputeResourceOperationRequest()->update([
                    "sub_status" => $subStatus,
                ]);
                return \App\Constants\ComputeResourceOperation\StatusCode::STATUS_FAILED;
            } else {
                throw $e;
            }
        }
    }
}