<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-20
 * Time: 下午6:05
 */

namespace App\Jobs\Volume;


class Detach extends OperationRequest
{
    protected function operationRequestHandle()
    {
        $this->getInstance()->createUtils()->detachVolume($this->getVolume()->unique_id);
        $this->getVolume()->update([
            "attached_compute_instance_id" => null,
        ]);
    }
}