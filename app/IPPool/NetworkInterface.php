<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-18
 * Time: 上午12:17
 */

namespace App\IPPool;


trait NetworkInterface
{
    public function networkInterface()
    {
        return $this->belongsTo(\App\ComputeInstance\Device\NetworkInterface::class, "nic_id");
    }
}