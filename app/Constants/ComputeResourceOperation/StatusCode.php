<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-28
 * Time: 下午7:56
 */

namespace App\Constants\ComputeResourceOperation;


interface StatusCode
{
    const STATUS_WAIT_FOR_SUBMITTING_TO_NODE = 0;
    const STATUS_WAIT_FOR_NODE_RESPONSE = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_FAILED = 3;
    const STATUS_WAIT_FOR_DISPATCHING = 5;
    const STATUS_PROCESSING = 6;
    const STATUS_WAIT_FOR_RETRY = 8;
}