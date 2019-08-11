<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-26
 * Time: 下午8:51
 */

namespace App\Jobs\ComputeInstance;


class ChangeOSPassword extends OperationRequest
{
    protected function operationRequestHandle()
    {
        $data = $this->getJSONDecodedData();
        $this->getComputeInstance()->createUtils()->changeOSPassword($data->password);
        $this->getComputeInstance()->update([
            "password" => $data->password,
        ]);
    }
}