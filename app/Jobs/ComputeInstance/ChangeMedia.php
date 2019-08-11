<?php

namespace App\Jobs\ComputeInstance;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use YunInternet\CCMSCommon\Constants\Domain\Device\Disk\DiskDeviceCode;

class ChangeMedia extends OperationRequest
{
    protected function operationRequestHandle()
    {
        $data = json_decode($this->getComputeInstanceOperationRequest()->data);
        $this->getComputeInstance()->createUtils()->changeMedia($data->diskDeviceCode, $data->deviceIndex, $data->mediaInternalName);
        switch ($data->diskDeviceCode) {
            case DiskDeviceCode::DEVICE_CDROM:
                $builder = $this->getComputeInstance()->cdroms();
                break;
            default:
                $builder = $this->getComputeInstance()->floppies();
        }
        $builder->offset($data->deviceIndex)->limit(1)->first()->update(["relative_media_id" => $data->mediaId]);
    }
}
