<?php

namespace App\Jobs\ComputeInstance;

use App\Utils\ComputeInstance\ConfigurationBuilder;

class UpdateIPAddress extends OperationRequest
{
    protected function operationRequestHandle()
    {
        $this->getComputeInstance()->createUtils()->updateIPAddress((new ConfigurationBuilder($this->getComputeInstance()))->networkInterfaces($this->getComputeInstanceOperationRequest()->data)[0]);
    }
}
