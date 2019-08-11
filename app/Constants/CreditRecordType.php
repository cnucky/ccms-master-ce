<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-4
 * Time: 上午12:42
 */

namespace App\Constants;


interface CreditRecordType
{
    const TYPE_ADD_CREDIT_VIA_PAYMENT_PLATFORM = 0;
    const TYPE_ADD_CREDIT_MANUALLY = 1;
    const TYPE_REFUND_VIA_PAYMENT_PLATFORM = 2;
    const TYPE_REFUND_MANUALLY = 3;
    const TYPE_FROZE_CREDIT = 5;
    const TYPE_UNFROZE_CREDIT = 6;
    const TYPE_REFUND_CANCELLATION = 8;

    const TYPE_CHARGE_COMPUTE_INSTANCE = 20;
    const TYPE_CHARGE_LOCAL_VOLUME = 21;
    const TYPE_CHARGE_ELASTIC_IP_V4 = 22;
    const TYPE_CHARGE_ELASTIC_IP_V6 = 23;
    const TYPE_CHARGE_PUBLIC_NETWORK_RX_TRAFFIC = 25;
    const TYPE_CHARGE_PUBLIC_NETWORK_TX_TRAFFIC = 26;

    const TYPE_CHARGE_MANUALLY = 127;

    const CONSUMPTION_CREDIT_RECORD_TYPE = [
        CreditRecordType::TYPE_CHARGE_COMPUTE_INSTANCE,
        CreditRecordType::TYPE_CHARGE_LOCAL_VOLUME,
        CreditRecordType::TYPE_CHARGE_ELASTIC_IP_V4,
        CreditRecordType::TYPE_CHARGE_ELASTIC_IP_V6,
        CreditRecordType::TYPE_CHARGE_PUBLIC_NETWORK_RX_TRAFFIC,
        CreditRecordType::TYPE_CHARGE_PUBLIC_NETWORK_TX_TRAFFIC,
        CreditRecordType::TYPE_CHARGE_MANUALLY,
    ];
}