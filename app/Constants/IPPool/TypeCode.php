<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-15
 * Time: 下午3:22
 */

namespace App\Constants\IPPool;


use YunInternet\CCMSCommon\Constants\NetworkType;

interface TypeCode
{
    const TYPE_PUBLIC_NETWORK = NetworkType::TYPE_PUBLIC_NETWORK;
    const TYPE_PRIVATE_NETWORK = NetworkType::TYPE_PRIVATE_NETWORK;
}