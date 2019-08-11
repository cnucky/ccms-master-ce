<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-8
 * Time: 下午3:27
 */

namespace App\Module\Resource\Payment;


use App\Module\LogLevel;
use App\PaymentModuleLog;
use App\Utils\Common;

class DatabaseLogger implements \App\Module\Contract\Logger
{
    private $paymentModuleId;

    public function __construct($paymentModuleId)
    {
        $this->paymentModuleId = $paymentModuleId;
    }

    /**
     * @inheritDoc
     */
    public function log($content, $identify = null, $type = null, $level = LogLevel::LEVEL_DEBUG)
    {
        try {
            if (!is_string($content)) {
                $content = json_encode($content);
            }
            PaymentModuleLog::query()->insert([
                "payment_module_id" => $this->paymentModuleId,
                "identify" => $identify,
                "level" => $level,
                "type" => $type,
                "log" => $content,
            ]);
        } catch (\Throwable $e) {
            Common::logException($e);
        }
    }
}