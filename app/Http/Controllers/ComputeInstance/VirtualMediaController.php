<?php

namespace App\Http\Controllers\ComputeInstance;

use App\ComputeInstance;
use App\Constants\ComputeInstance\OperationRequest\TypeCode;
use App\Constants\GlobalErrorCode;
use App\PublicFloppy;
use App\PublicISO;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use YunInternet\CCMSCommon\Constants\Domain\Device\Disk\DiskDeviceCode;

class VirtualMediaController extends Controller
{
    public function changeCDROMMedia(Request $request, ComputeInstance $computeInstance)
    {
        // Check whether target cdrom is exists
        $cdrom = $computeInstance->cdroms()->offset($request->index)->limit(1)->first();
        if (is_null($cdrom))
            return ["result" => false, "errno" => GlobalErrorCode::CDROM_NOT_EXISTS, "message" => "CDROM not exists"];

        $mediaInternalName = null;
        if (is_numeric($request->mediaId)) {
            $media = PublicISO::query()->find($request->mediaId);
            if (is_null($media))
                return ["result" => false, "errno" => GlobalErrorCode::MEDIA_NOT_EXISTS, "message" => "Media not exists"];
            $mediaInternalName = $media->internal_name;
        }

        $operationRequest = $this->createChangeMediaRequest($computeInstance, DiskDeviceCode::DEVICE_CDROM, $request->index, $request->mediaId, $mediaInternalName);

        return ["result" => true, "operationRequest" => $operationRequest];
    }

    public function changeFloppyMedia(Request $request, ComputeInstance $computeInstance)
    {
        // Check whether target floppy is exists
        $floppy = $computeInstance->floppies()->offset($request->index)->limit(1)->first();
        if (is_null($floppy))
            return ["result" => false, "errno" => GlobalErrorCode::FLOPPY_NOT_EXISTS, "message" => "Floppy not exists"];

        $mediaInternalName = null;
        if (is_numeric($request->mediaId)) {
            $media = PublicFloppy::query()->find($request->mediaId);
            if (is_null($media))
                return ["result" => false, "errno" => GlobalErrorCode::MEDIA_NOT_EXISTS, "message" => "Media not exists"];
            $mediaInternalName = $media->internal_name;
        }

        $operationRequest = $this->createChangeMediaRequest($computeInstance, DiskDeviceCode::DEVICE_FLOPPY, $request->index, $request->mediaId, $mediaInternalName);

        return ["result" => true, "operationRequest" => $operationRequest];
    }

    private function createChangeMediaRequest(ComputeInstance $computeInstance, $diskDeviceCode, $deviceIndex, $mediaId, $mediaInternalName)
    {
        return ComputeInstance\OperationRequest::newRequestThenDispatch($computeInstance, TypeCode::TYPE_CHANGE_MEDIA, [
            "diskDeviceCode" => $diskDeviceCode,
            "deviceIndex" => $deviceIndex,
            "mediaId" => $mediaId,
            "mediaInternalName" => $mediaInternalName
        ]);
    }
}
