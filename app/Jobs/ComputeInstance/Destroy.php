<?php

namespace App\Jobs\ComputeInstance;

use App\ComputeInstance\Device\NetworkInterface;
use App\Constants\ComputeInstanceStatusCode;

class Destroy extends OperationRequest
{
    protected function operationRequestHandle()
    {
        $data = json_decode($this->getComputeInstanceOperationRequest()->data);

        $this->getComputeInstance()->update([
            "status" => ComputeInstanceStatusCode::STATUS_DESTROYING,
        ]);

        $this->getComputeInstance()->createUtils()->destroy(@$data->deleteAttachedVolumes);

        if (@$data->deleteAttachedVolumes) {
            foreach ($this->getComputeInstance()->attachedLocalVolumes as $attachedLocalVolume) {
                $attachedLocalVolume->deleteWithNodeCounterUpdate();
            }
        } else {
            $this->getComputeInstance()->attachedLocalVolumes()->update(["attached_compute_instance_id" => null]);
        }

        /**
         * @var NetworkInterface $networkInterface
         */
        if (@$data->releaseExtraIPs) {
            foreach ($this->getComputeInstance()->networkInterfaces as $networkInterface) {
                $networkInterface->releaseIPAddresses();
            }
        } else {
            foreach ($this->getComputeInstance()->networkInterfaces as $networkInterface) {
                $networkInterface->unbindIPAddresses();
                // Release unbindable addresses
                $networkInterface->releaseIPAddresses(function ($query) {
                    $query->where("unbindable", 0);
                });
            }
        }

        $this->getComputeInstance()->deleteWithNodeCounterUpdate();
    }

    protected function onFailed()
    {
        try {
            $this->getComputeInstance()->update(["status" => ComputeInstanceStatusCode::STATUS_DESTROY_UNSUCCESSFULLY]);
        } catch (\Throwable $e) {
        }
    }
}
