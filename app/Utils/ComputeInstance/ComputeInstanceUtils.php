<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-28
 * Time: 下午9:15
 */

namespace App\Utils\ComputeInstance;


use App\ComputeInstance;
use App\Utils\Node\ComputeNode\ComputeNodeUtil;
use App\Utils\Node\ComputeNode\Exception\Exception;
use YunInternet\CCMSCommon\Constants\ErrorSource;
use YunInternet\Libvirt\Exception\ErrorCode;

class ComputeInstanceUtils
{
    private $uniqueId;

    private $computeNodeUtil;

    public function __construct($uniqueId, ComputeNodeUtil $computeNodeUtil)
    {
        $this->uniqueId = $uniqueId;

        $this->computeNodeUtil = $computeNodeUtil;
    }

    public static function fromComputeInstanceModel(ComputeInstance $computeInstance)
    {
        return new self($computeInstance->unique_id, $computeInstance->node->createUtil());
    }

    public function powerOn($setFirstBoot = false)
    {
        return $this->powerAction("on", [
            "setFirstBoot" => $setFirstBoot
        ]);
    }

    public function powerOff()
    {
        return $this->powerAction("off");
    }

    public function powerReset()
    {
        return $this->powerAction("reset");
    }

    /**
     * @param $action
     * @param mixed $data
     * @return bool
     * @throws Exception
     * @throws \YunInternet\CCMSCommon\Network\Exception\APIRequestException
     */
    public function powerAction($action, $data = [])
    {
        $response = $this->computeNodeUtil->makeAPIRequest("computeInstances/" . $this->uniqueId . "/power/" . $action)->withPostFields($data, true)->JSONResponse($rawResponse);
        return ComputeNodeUtil::responseHandler($response, [
            ErrorCode::DOMAIN_IS_NOT_RUNNING,
            ErrorCode::DOMAIN_IS_ALREADY_RUNNING,
        ]);
    }

    public function changeHostname($hostname)
    {
        $response = $this->makeComputeInstanceAPIRequest("changeHostname")
            ->withPostFields(["hostname" => $hostname])
            ->JSONResponse()
        ;
        return ComputeNodeUtil::responseHandler($response);
    }

    public function changeOSPassword($password)
    {
        $response = $this->makeComputeInstanceAPIRequest("changeOSPassword")
            ->withPostFields(["password" => $password])
            ->JSONResponse()
        ;
        return ComputeNodeUtil::responseHandler($response);
    }

    public function reconfigureOSNetwork()
    {
        $response = $this->makeComputeInstanceAPIRequest("reconfigureOSNetwork")
            ->withPostFields('')
            ->JSONResponse()
        ;
        return ComputeNodeUtil::responseHandler($response);
    }

    /**
     * @param int $diskDeviceCode in YunInternet\CCMSCommon\Constants\Domain\Device\Disk\DiskDeviceCode
     * @param int $index
     * @param string $media set null to eject media
     * @return true
     * @throws \Exception
     */
    public function changeMedia($diskDeviceCode, $index, $media = null)
    {
        $response = $this->makeComputeInstanceAPIRequest("media/" . $diskDeviceCode . "/" . $index . "/" . $media)->JSONResponse($rawResponse);
        return ComputeNodeUtil::responseHandler($response);
    }

    public function updateIPAddress($networkInterfaceConfiguration)
    {
        $response = $this->makeComputeInstanceAPIRequest("networkInterfaces/updateIPAddresses")
            ->withPostFields($networkInterfaceConfiguration, true)
            ->JSONResponse($rawResponse)
        ;
        return ComputeNodeUtil::responseHandler($response);

    }

    public function changeNetworkInterfaceModel($macAddress, $modelCode)
    {
        $response = $this->makeComputeInstanceAPIRequest("networkInterfaces/changeModel")
            ->withPostFields(["mac_address" => $macAddress, "model" => $modelCode], true)
            ->JSONResponse()
        ;
        return ComputeNodeUtil::responseHandler($response);
    }

    public function attachVolume($volumeUniqueId, $bus)
    {
        $response = $this->makeComputeInstanceAPIRequest("volumes/" . $volumeUniqueId . "/attach")
            ->withPostFields(["bus" => $bus])
            ->JSONResponse()
        ;
        return ComputeNodeUtil::responseHandler($response);
    }

    public function detachVolume($volumeUniqueId)
    {
        $response = $this->makeComputeInstanceAPIRequest("volumes/". $volumeUniqueId ."/detach")
            ->JSONResponse()
        ;
        return ComputeNodeUtil::responseHandler($response, ErrorCode::STORAGE_VOLUME_NOT_FOUND);
    }

    public function reconfigure(ComputeInstance $computeInstance)
    {
        return $this->computeNodeUtil->instanceReconfigureRequest($this->uniqueId, ConfigurationBuilder::buildConfiguration($computeInstance));
    }

    /**
     * Delete compute instance
     * @param bool $deleteAttachedVolumes
     * @return bool
     * @throws Exception
     * @throws \YunInternet\CCMSCommon\Network\Exception\APIRequestException
     */
    public function destroy(bool $deleteAttachedVolumes)
    {
        return $this->computeNodeUtil->deleteInstance($this->uniqueId, $deleteAttachedVolumes);
    }


    private function makeComputeInstanceAPIRequest($uri)
    {
        $uri = ltrim($uri, "/");
        return $this->computeNodeUtil->makeAPIRequest("computeInstances/" . $this->uniqueId . "/" . $uri);
    }
}