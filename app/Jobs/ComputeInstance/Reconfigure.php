<?php

namespace App\Jobs\ComputeInstance;

use App\ComputeInstance;
use App\Constants\ComputeInstanceStatusCode;
use App\Utils\ComputeInstance\ConfigurationBuilder;

class Reconfigure extends OperationRequest
{
    protected function operationRequestHandle()
    {
        $data = json_decode($this->getComputeInstanceOperationRequest()->data);

        if (@$data->ejectMedias) {
            $this->getComputeInstance()->cdroms()->update([
                "relative_media_id" => null,
            ]);
            $this->getComputeInstance()->floppies()->update([
                "relative_media_id" => null,
            ]);
        }

        $this->getComputeNode()->createUtil()->instanceReconfigureRequest($this->getComputeInstance()->unique_id, ConfigurationBuilder::buildConfiguration($this->getComputeInstance()));
    }

    protected function onCompleted()
    {
        ComputeInstance::query()
            ->where([
                "id" => $this->getComputeInstance()->id,
                "status" => ComputeInstanceStatusCode::STATUS_CONFIGURING,
            ])
            ->update([
                "status" => ComputeInstanceStatusCode::STATUS_NORMAL,
            ])
        ;
    }
}
