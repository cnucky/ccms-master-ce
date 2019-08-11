<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-23
 * Time: 下午2:06
 */

namespace App\Jobs\ComputeInstance;


class ReconfigureOSNetwork extends OperationRequest
{
    protected function operationRequestHandle()
    {
        $this->getComputeInstance()->createUtils()->reconfigureOSNetwork();
    }
}