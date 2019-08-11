<?php

namespace App\Jobs\Volume;

use App\Constants\Volume\OperationRequest\SubStatus;
use App\Constants\Volume\StatusCode;
use App\Utils\Node\ComputeNode\ComputeNodeUtil;
use App\Utils\Node\ComputeNode\Exception\Exception;
use App\Utils\Volume\Constants;
use YunInternet\CCMSCommon\Constants\ErrorSource;
use YunInternet\Libvirt\Exception\ErrorCode;

class NewLocalVolume extends OperationRequest
{
    protected function operationRequestHandle()
    {
        try {
            $this->getUtils()->newVolume($this->getVolume()->capacity * 1024, $this->getVolume()->instance->unique_id, $this->getVolume()->bus);
            $this->getVolume()->update([
                "status" => Constants::STATUS_NORMAL
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

    protected function onFailed()
    {
        $this->getVolume()->deleteWithNodeCounterUpdate();
    }
}
