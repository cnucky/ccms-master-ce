<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-18
 * Time: 下午8:41
 */

namespace App\Utils\Network;


use App\APIRequestLog;
use YunInternet\CCMSCommon\Network\CURLAPIRequest;

class CURLAPIRequestWithLogger extends CURLAPIRequest
{
    private $APIRequestLog;

    public function __construct($ch, APIRequestLog $operationRequestLog = null)
    {
        parent::__construct($ch);

        $this->APIRequestLog = $operationRequestLog;
    }

    public function response(&$error = null, &$errno = null)
    {
        $response = parent::response($error, $errno);
        $this->saveRawResponse($response);
        return $response;
    }

    public function responseOrFail()
    {
        $response = parent::responseOrFail();
        $this->saveRawResponse($response);
        return $response;
    }

    public function JSONResponse(&$rawResponse = null)
    {
        $response = parent::JSONResponse($rawResponse);
        $this->saveRawResponse($rawResponse);
        return $response;
    }

    private function saveRawResponse(&$rawResponse)
    {
        if ($this->APIRequestLog)
            $this->APIRequestLog->update(["raw_response" => $rawResponse]);
    }
}