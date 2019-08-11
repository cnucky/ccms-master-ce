<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-1
 * Time: 下午3:28
 */

namespace App\Utils\Node\ComputeNode;


use App\Utils\Node\ComputeNode\Exception\Exception;
use App\Utils\PKIBuilder;
use YunInternet\CCMSCommon\Constants\ErrorSource;
use YunInternet\CCMSCommon\Constants\SlaveType;
use YunInternet\CCMSCommon\Network\Exception\APIRequestException;
use YunInternet\Libvirt\Connection;
use YunInternet\Libvirt\Exception\ErrorCode;

class ComputeNodeUtil
{
    private $host;

    private $PKIBuilder;

    private $APIRequestFactory;

    public function __construct($host, PKIBuilder $PKIBuilder)
    {
        $this->host = $host;

        $this->PKIBuilder = $PKIBuilder;

        $this->APIRequestFactory = new APIRequestFactory($PKIBuilder);
    }

    /**
     * @return Connection
     */
    public function libvirtConnection()
    {
        static $libvirtConnection = null;
        if (is_null($libvirtConnection))
            $libvirtConnection = new Connection("qemu+tls://" . $this->host . "/system?pkipath=" . $this->PKIBuilder->pkiDirectory);
        return $libvirtConnection;
    }

    /**
     * @param mixed $rawResponse
     * @return bool
     * @throws APIRequestException
     */
    public function ping(&$rawResponse = null)
    {
        $response = $this->APIRequestFactory->create($this->createURL("ping"))->JSONResponse($rawResponse);
        if (@$response->result)
            return true;
        return false;
    }

    public function getNodeUUID()
    {
        $response = $this->makeAPIRequest("ccms/slave/uuid")->JSONResponse();
        if (@$response->result)
            return $response->uuid;
        throw new Exception($response->message);
    }

    /**
     * @param string $systemId
     * @param int|string $id
     * @param string $masterHost
     * @param string|null $token
     * @return bool|string
     * @throws APIRequestException
     * @throws Exception
     */
    public function registerMaster($systemId, $id, $masterHost, $token = null)
    {
        $response = $this->APIRequestFactory->make("https://" . $this->host . ":2048/api/masterServers/register", ["masterId" => $systemId, "id" => $id, "host" => $masterHost, "slaveType" => SlaveType::COMPUTE_NODE, "token" => $token])->JSONResponse($rawResponse);

        if (@$response->result)
            return $response;
        throw new Exception($response->message);
    }

    public function updateNoVNCConfigurations($port, $certificate, $privateKey)
    {
        $response = $this->makeAPIRequest("noVNC/configurations")
            ->withPostFields([
                "port" => $port,
                "certificate" => $certificate,
                "privateKey" => $privateKey,
            ], true)
            ->JSONResponse()
        ;
        return self::responseHandler($response);
    }

    /**
     * @param $configuration
     * @return mixed
     * @throws Exception
     * @throws \YunInternet\CCMSCommon\Network\Exception\APIRequestException
     */
    public function instanceSetupRequest($configuration)
    {
        $response = $this->APIRequestFactory->make($this->createURL("computeInstances/setupRequests"))->withPostFields($configuration, true)->JSONResponse($rawResponse);
        if (@$response->result)
            return $response;
        throw new Exception(@$response->message);
    }

    public function instanceReconfigureRequest($uniqueId, $configuration)
    {
        $response = $this->APIRequestFactory->make($this->createURL("computeInstances/". $uniqueId ."/reconfigure"))->withPostFields($configuration, true)->JSONResponse($rawResponse);
        if (@$response->result)
            return $response->domainXML;
        throw new Exception(@$response->message);
    }

    /**
     * @param $uniqueId
     * @param bool $deleteAttachedVolumes
     * @return bool
     * @throws Exception
     * @throws \YunInternet\CCMSCommon\Network\Exception\APIRequestException
     */
    public function deleteInstance($uniqueId, $deleteAttachedVolumes = false)
    {
        $APIRequest = $this->APIRequestFactory->make($this->createURL("computeInstances/" . $uniqueId . "/delete"));
        $APIRequest->withPostFields([
            "deleteAttachedVolumes" => $deleteAttachedVolumes,
        ], true);
        $response = $APIRequest->JSONResponse($rawResponse);
        return self::responseHandler($response, ErrorCode::DOMAIN_NOT_FOUND);
    }

    public function nodeInfo()
    {
        return $this->libvirtConnection()->libvirt_node_get_info();
    }

    /**
     * @param $uri
     * @param null $data
     * @param null $headers
     * @param null $setOptions
     * @return \YunInternet\CCMSCommon\Network\APIRequest|\YunInternet\CCMSCommon\Network\CURLAPIRequest
     */
    public function makeAPIRequest($uri, $data = null, $headers = null, $setOptions = null)
    {
        return $this->APIRequestFactory->make($this->createURL($uri), $data, $headers, $setOptions);
    }

    /**
     * @param \stdClass $response
     * @param int|null $expectErrnos Expect errno, if null, always return false
     * @return int|false Return the found errno, or false on not an expect errno found
     */
    public static function isLibvirtErrnos($response, $expectErrnos)
    {
        if (is_null($expectErrnos))
            return false;
        if (@$response->source !== ErrorSource::SOURCE_LIBVIRT)
            return false;

        if (!is_array($expectErrnos))
            $expectErrnos = [$expectErrnos];

        foreach ($expectErrnos as $errno) {
            if (@$response->errno === $errno)
                return $errno;
        }

        return false;
    }

    /**
     * @param \stdClass $response
     * @param int|int[]|null $acceptableErrnos
     * @return bool
     * @throws Exception
     */
    public static function responseHandler($response, $acceptableErrnos = null)
    {
        if (@$response->result)
            return true;
        if (self::isLibvirtErrnos($response, $acceptableErrnos) !== false)
            return true;
        throw new Exception($response->message, 0, null, $response);
    }

    private function createURL($uri)
    {
        $uri = ltrim($uri, "/");
        return sprintf("https://%s:2048/api/%s", $this->host, $uri);
    }
}