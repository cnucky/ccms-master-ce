<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-1
 * Time: 下午11:04
 */

namespace App\Utils\Node\ComputeNode;


use App\APIRequestLog;
use App\Utils\Network\CURLAPIRequestWithLogger;
use App\Utils\PKIBuilder;
use YunInternet\CCMSCommon\Network\CommonHeader;
use YunInternet\CCMSCommon\Network\CommonOption;
use YunInternet\CCMSCommon\Network\CURLAPIRequest;
use YunInternet\CCMSCommon\Network\Utils\CURLCommon;

class APIRequestFactory implements \YunInternet\CCMSCommon\Network\Contract\APIRequestFactory
{
    use CommonHeader;

    use CommonOption;

    private $PKIBuilder;

    private $cURLCommonOptions;

    public function __construct(PKIBuilder $PKIBuilder)
    {
        $this->PKIBuilder = $PKIBuilder;

        // All API request require client-side certificate authentication and return transfer
        $this
            ->addCommonOption(CURLOPT_CAINFO, $PKIBuilder->CACertificateFilePath)
            ->addCommonOption(CURLOPT_SSLKEY, $PKIBuilder->clientPrivateKeyFilePath)
            ->addCommonOption(CURLOPT_SSLCERT, $PKIBuilder->clientCertificateFilePath)
            ->addCommonOption(CURLOPT_RETURNTRANSFER, true)
        ;

    }

    /**
     * @inheritdoc
     */
    public function make($url, $data = null, $headers = null, $setOptions = null)
    {
        $ch = curl_init($url);

        // Set common options
        curl_setopt_array($ch, $this->commonOptions);

        // Set postfields
        $additionalHeaders = CURLCommon::setPostfieldsIfNeed($ch, $data);

        $cURLAPIRequest = new CURLAPIRequestWithLogger($ch, APIRequestLog::query()->create([
            "url" => $url,
        ]));
        // Add headers
        $cURLAPIRequest->setHeaderLists($this->commonHeaders, $additionalHeaders);

        return $cURLAPIRequest;
    }
}