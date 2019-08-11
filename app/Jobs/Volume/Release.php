<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-20
 * Time: 下午6:44
 */

namespace App\Jobs\Volume;


class Release extends OperationRequest
{
    protected function operationRequestHandle()
    {
        $detachFrom = null;
        if ($this->getInstance()) {
            $detachFrom = $this->getInstance()->unique_id;
        }

        $this->getUtils()->release($detachFrom);
        $this->getVolume()->deleteWithNodeCounterUpdate();
    }
}