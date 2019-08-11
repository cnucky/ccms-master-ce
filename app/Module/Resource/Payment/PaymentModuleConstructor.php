<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-7
 * Time: 下午4:13
 */

namespace App\Module\Resource\Payment;



use App\Module\Contract\Logger;

trait PaymentModuleConstructor
{
    private $logger;

    private $moduleSettings;

    private $currencyCode;

    public function __construct(Logger $logger, $moduleSettings, $currencyCode)
    {
        $this->logger = $logger;

        $this->moduleSettings = $moduleSettings;

        $this->currencyCode = $currencyCode;
    }
}