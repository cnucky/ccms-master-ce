<?php

namespace App\Jobs\Volume;

use App\Constants\ComputeResourceOperation\StatusCode;
use App\Constants\Volume\OperationRequest\SubStatus;
use App\Utils\Node\ComputeNode\ComputeNodeUtil;
use App\Utils\Node\ComputeNode\Exception\Exception;
use YunInternet\Libvirt\Exception\ErrorCode;

class Resize extends OperationRequest
{
    protected function operationRequestHandle()
    {
        $data = json_decode($this->getData());

        try {
            $this->getUtils()->resize($data->newCapacity);
        } catch (Exception $e) {
            if (ComputeNodeUtil::isLibvirtErrnos($e->getResponse(), ErrorCode::ANOTHER_PROCESS_USING_THE_IMAGE) !== false) {
                $this->getComputeResourceOperationRequest()->update([
                    "sub_status" => SubStatus::SUB_STATUS_ANOTHER_PROCESS_USING_THE_IMAGE,
                ]);
                return StatusCode::STATUS_FAILED;
            } else {
                throw $e;
            }
        }
    }

    protected function onFailed()
    {
        $data = $this->getJSONDecodedData();
        if ($data->originCapacity === $this->getVolume()->capacity) {
            return;
        }
        $this->getComputeNode()->releaseDiskCapacity($data->diffCapacity, function () use (&$data) {
            $this->getVolume()->update(["capacity" => $data->originCapacity]);
        });
    }
}
