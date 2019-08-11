<?php

namespace App\Jobs\ComputeInstance;

use App\ComputeInstance;
use App\Constants\ComputeInstance\OperationRequest\LogLevel;
use App\Constants\ComputeInstance\OperationRequest\StatusCode;
use App\Jobs\ComputeResourceOperationRequest;
use App\Node\ComputeNode;
use YunInternet\CCMSCommon\Network\Exception\APIRequestException;

/**
 * Trait OperationRequest
 * IMPORTANT: use protected properties if you need the properties encoded and stored into database
 * @package App\Jobs\ComputeInstance
 */
abstract class OperationRequest extends ComputeResourceOperationRequest
{
    public function getComputeInstanceOperationRequest(): \App\ComputeResourceOperationRequest
    {
        return $this->getComputeResourceOperationRequest();
    }

    public function getComputeInstance() : ComputeInstance
    {
        return $this->getComputeInstanceOperationRequest()->resource;
    }

    public function getComputeNode() : ComputeNode
    {
        return $this->getComputeInstance()->node;
    }
}
