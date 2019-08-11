<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-21
 * Time: 上午2:07
 */

namespace App\Constants\Volume\OperationRequest;


use App\Exceptions\InsufficientResourceException;
use YunInternet\Libvirt\Exception\ErrorCode;

interface SubStatus
{
    const SUB_STATUS_NO_MORE_AVAILABLE_PCI_SLOTS = 1;
    const SUB_STATUS_ANOTHER_PROCESS_USING_THE_IMAGE = 2;
    const BUS_SATA_CAN_NOT_BE_HOT_PLUGGED = 3;
    const SUB_STATUS_INSUFFICIENT_RESOURCE = 5;

    const LIBVIRT_ERRNO_MAP_2_SUB_STATUS = [
        ErrorCode::NO_MORE_AVAILABLE_PCI_SLOTS => self::SUB_STATUS_NO_MORE_AVAILABLE_PCI_SLOTS,
        ErrorCode::ANOTHER_PROCESS_USING_THE_IMAGE => self::SUB_STATUS_ANOTHER_PROCESS_USING_THE_IMAGE,
        ErrorCode::BUS_SATA_CAN_NOT_BE_HOT_PLUGGED => self::BUS_SATA_CAN_NOT_BE_HOT_PLUGGED,
    ];
}