<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-26
 * Time: 下午7:34
 */

namespace App\Jobs\ComputeInstance;


class ChangeHostname extends OperationRequest
{
    protected function operationRequestHandle()
    {
        $data = $this->getJSONDecodedData();
        $this->getComputeInstance()->createUtils()->changeHostname($data->hostname);
        $this->getComputeInstance()->update([
            "hostname" => $data->hostname,
        ]);
    }
}