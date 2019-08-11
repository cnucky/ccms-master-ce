<?php

namespace App\Jobs\Volume;


use App\ComputeInstance;
use App\ComputeInstance\LocalVolume;
use App\Jobs\ComputeResourceOperationRequest;
use App\Node\ComputeNode;
use App\Utils\Volume\LocalVolumeUtils;

abstract class OperationRequest extends ComputeResourceOperationRequest
{
    public function getVolume() : LocalVolume
    {
        return $this->getComputeResourceOperationRequest()->resource;
    }

    public function getComputeNode() : ComputeNode
    {
        return $this->getVolume()->node;
    }

    /**
     * @return ComputeInstance|null
     */
    public function getInstance()
    {
        return $this->getVolume()->instance;
    }

    public function getUtils() : LocalVolumeUtils
    {
        return LocalVolumeUtils::fromLocalVolumeModel($this->getVolume());
    }
}
