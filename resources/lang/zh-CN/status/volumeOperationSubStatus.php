<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-21
 * Time: 上午2:15
 */

return [
    \App\Constants\Volume\OperationRequest\SubStatus::SUB_STATUS_NO_MORE_AVAILABLE_PCI_SLOTS => "卷连接实例失败：目标实例PCI控制器插槽不足，请关机，或使用SCSI总线再次尝试",
    \App\Constants\Volume\OperationRequest\SubStatus::SUB_STATUS_ANOTHER_PROCESS_USING_THE_IMAGE => "无法对目标卷[{0}]进行操作，请检查其所连接的实例是否已关机",
    \App\Constants\Volume\OperationRequest\SubStatus::BUS_SATA_CAN_NOT_BE_HOT_PLUGGED => "SATA总线当前无法进行热插拔，请关闭实例的电源后再次尝试",
];