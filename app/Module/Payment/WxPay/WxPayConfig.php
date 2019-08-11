<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-7
 * Time: 上午2:24
 */

namespace App\Module\Payment\WxPay;


class WxPayConfig extends \WxPayConfigInterface
{
    public $appId;

    public $merchantId;

    public $merchantKey;

    public $notifyURL;

    public function __construct($appId, $merchantId, $merchantKey)
    {
        $this->appId = $appId;
        $this->merchantId = $merchantId;
        $this->merchantKey = $merchantKey;
    }

    /**
     * @inheritDoc
     */
    public function GetAppId()
    {
        return $this->appId;
    }

    public function GetMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * @inheritDoc
     */
    public function GetNotifyUrl()
    {
        return $this->notifyURL;
    }

    public function GetSignType()
    {
        return "HMAC-SHA256";
    }

    /**
     * @inheritDoc
     */
    public function GetProxy(&$proxyHost, &$proxyPort)
    {
        $proxyHost = "0.0.0.0";
        $proxyPort = 0;
    }

    /**
     * @inheritDoc
     */
    public function GetReportLevenl()
    {
        return 1;
    }

    public function GetKey()
    {
        return $this->merchantKey;
    }

    public function GetAppSecret()
    {
        // TODO: Implement GetAppSecret() method.
    }

    /**
     * @inheritDoc
     */
    public function GetSSLCertPath(&$sslCertPath, &$sslKeyPath)
    {
        $sslCertPath = __DIR__ . "/certificate/certificate.pem";
        $sslKeyPath = __DIR__ . "/certificate/privateKey.pem";
    }
}