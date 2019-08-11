<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-4
 * Time: 下午9:49
 */

return [
    \App\Constants\CreditRecordType::TYPE_ADD_CREDIT_VIA_PAYMENT_PLATFORM => "自助充值",
    \App\Constants\CreditRecordType::TYPE_ADD_CREDIT_MANUALLY => "充值",
    \App\Constants\CreditRecordType::TYPE_REFUND_VIA_PAYMENT_PLATFORM => "退款",
    \App\Constants\CreditRecordType::TYPE_REFUND_MANUALLY => "退款",
    \App\Constants\CreditRecordType::TYPE_FROZE_CREDIT => "余额冻结",
    \App\Constants\CreditRecordType::TYPE_UNFROZE_CREDIT => "余额解冻",
    \App\Constants\CreditRecordType::TYPE_REFUND_CANCELLATION => "取消退款",

    \App\Constants\CreditRecordType::TYPE_CHARGE_COMPUTE_INSTANCE => "计算实例",
    \App\Constants\CreditRecordType::TYPE_CHARGE_LOCAL_VOLUME => "本地卷",
    \App\Constants\CreditRecordType::TYPE_CHARGE_ELASTIC_IP_V4 => "弹性IPv4",
    \App\Constants\CreditRecordType::TYPE_CHARGE_ELASTIC_IP_V6 => "弹性IPv6",
    \App\Constants\CreditRecordType::TYPE_CHARGE_PUBLIC_NETWORK_RX_TRAFFIC => "下行流量",
    \App\Constants\CreditRecordType::TYPE_CHARGE_PUBLIC_NETWORK_TX_TRAFFIC => "上行流量",

    \App\Constants\CreditRecordType::TYPE_CHARGE_MANUALLY => "其它",
];