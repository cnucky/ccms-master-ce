<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-18
 * Time: 下午8:28
 */

namespace App\Utils\Volume;


use App\ComputeInstance\LocalVolume;
use App\Utils\Node\ComputeNode\ComputeNodeUtil;
use App\Utils\Node\ComputeNode\Exception\Exception;
use YunInternet\Libvirt\Exception\ErrorCode;

class LocalVolumeUtils
{
    private $uniqueId;

    private $computeNodeUtil;

    public function __construct($uniqueId, ComputeNodeUtil $computeNodeUtil)
    {
        $this->uniqueId = $uniqueId;

        $this->computeNodeUtil = $computeNodeUtil;
    }

    public static function fromLocalVolumeModel(LocalVolume $localVolume)
    {
        return new self($localVolume->unique_id, $localVolume->node->createUtil());
    }


    public function newVolume($capacityWithMiBUnit, $attach2Instance = null, $bus = null)
    {
        return ComputeNodeUtil::responseHandler($this->computeNodeUtil->makeAPIRequest("storageVolumes/new")->withPostFields([
            "uniqueId" => $this->uniqueId,
            "capacity" => $capacityWithMiBUnit,
            "attach2Instance" => $attach2Instance,
            "bus" => $bus,
        ], true)->JSONResponse());
    }

    public function resize($newCapacity, $allocate = false, $relativeSize = false, $shrink = false)
    {
        return ComputeNodeUtil::responseHandler($this->makeAPIRequest("resize")
            ->withPostFields([
                "capacity" => $newCapacity,
                "allocate" => $allocate,
                "relativeSize" => $relativeSize,
                "shrink" => $shrink,
            ], true)
            ->JSONResponse($rawResponse))
        ;
    }

    public function recreate($capacityWithMiBUnit, $withPublicImage = null)
    {
        return ComputeNodeUtil::responseHandler(
            $this->makeAPIRequest("recreate")
            ->withPostFields([
                "capacity" => $capacityWithMiBUnit,
                "publicImage" => $withPublicImage,
            ], true)
            ->JSONResponse()
        );
    }

    public function release($detachFrom = null)
    {
        return ComputeNodeUtil::responseHandler($this->makeAPIRequest("release")
            ->withPostFields([
                "detachFrom" => $detachFrom
            ], true)
            ->JSONResponse()
        , ErrorCode::STORAGE_VOLUME_NOT_FOUND);
    }

    private function makeAPIRequest($uri)
    {
        $uri = ltrim($uri, "/");
        return $this->computeNodeUtil->makeAPIRequest("storageVolumes/" . $this->uniqueId . "/" . $uri);
    }
}