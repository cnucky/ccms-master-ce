<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-26
 * Time: 下午10:13
 */

namespace App\Jobs\ComputeInstance;


use App\ComputeInstance\LocalVolume;
use App\Constants\ComputeInstanceStatusCode;
use App\Image;
use App\Utils\Volume\LocalVolumeUtils;

class ChangePublicImage extends OperationRequest
{
    protected function operationRequestHandle()
    {
        $data = $this->getJSONDecodedData();
        /**
         * @var Image $image
         */
        $image = Image::query()->findOrFail($data->imageId);
        /**
         * @var LocalVolume $volume
         */
        $volume = LocalVolume::query()->findOrFail($data->volumeId);

        $computeInstanceUtils = $this->getComputeInstance()->createUtils();

        $computeInstanceUtils->powerOff();

        $this->getComputeInstance()->createUtils()->reconfigure($this->getComputeInstance());
        $volumeUtils = LocalVolumeUtils::fromLocalVolumeModel($volume);

        $volumeUtils->recreate($volume->capacity * 1024, $image->internal_name);
        $volume->update([
            "image_id" => $image->id,
        ]);

        $computeInstanceUtils->powerOn(true);

        $this->getComputeInstance()->update([
            "power_status" => ComputeInstanceStatusCode::POWER_STATUS_RUNNING,
        ]);
    }
}