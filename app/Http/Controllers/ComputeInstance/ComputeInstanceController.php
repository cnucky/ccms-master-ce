<?php

namespace App\Http\Controllers\ComputeInstance;

use App\ComputeInstance;
use App\ComputeInstancePackage;
use App\ComputeInstancePackageCategory;
use App\ComputeResourceOperationRequest;
use App\Constants\CommonStatusCode;
use App\Constants\ComputeInstance\OperationRequest\TypeCode;
use App\Constants\ComputeInstanceStatusCode;
use App\Constants\ComputeResourceOperation\ResourceTypeCode;
use App\Constants\GlobalErrorCode;
use App\Exceptions\CCMSAPIException;
use App\Exceptions\InsufficientResourceException;
use App\Exceptions\MinCreditRequired;
use App\Exceptions\PackageOutOfStockException;
use App\Image;
use App\ImageCategory;
use App\Node\ComputeNode;
use App\Utils\ComputeInstance\ComputeInstances;
use App\Zone;
use App\ZoneHasComputeInstancePackage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use YunInternet\CCMSCommon\Constants\MachineType;
use YunInternet\CCMSCommon\Constants\VolumeBusCode;
use YunInternet\CCMSCommon\NOVNC\Authenticator;

class ComputeInstanceController extends Controller
{
    /*
    public function __construct()
    {
        $this
            ->middleware('can:operate,computeInstance')
            ->except([])
        ;
    }
    */

    public function settingAvailableData(Request $request, ComputeInstance $computeInstance)
    {
        /**
         * @var Zone $zone
         */
        $zone = $computeInstance->node->zone;
        $availablePackageIdList = $zone->packages()->get()->pluck("id")->toArray();
        return [
            "result" => true,
            "packageCategories" => ComputeInstancePackageCategory::query()
                ->with(["packages" => function ($builder) use (&$availablePackageIdList) {
                    $builder->whereIn("id", $availablePackageIdList);
                }, "packages.stocks" => function ($builder) use ($zone) {
                    $builder->where("zone_id", $zone->id);
                }])
                ->get(),
            "imageCategories" => ImageCategory::query()
                ->where("status", CommonStatusCode::AVAILABLE)
                ->with(["images" => function ($builder) {
                    $builder
                        ->where("status", CommonStatusCode::AVAILABLE)
                        ->orderByDesc("order")
                    ;
                }])->orderByDesc("order")->get(),
        ];
    }

    public function statistics(Request $request, ComputeInstance $computeInstance)
    {
        return [
            "result" => true,
            "cpuUsages" => $computeInstance->cpuUsages()->where($this->pass6HoursClosure())->get(),
            "diskIOUsages" => $computeInstance->diskIOUsages()->where($this->pass6HoursClosure())->get(),
            "networks" => $computeInstance->networkInterfaces()->with([
                "trafficUsages" => $this->pass6HoursClosure(),
                "bandwidthUsages" => $this->pass6HoursClosure(),
            ])->get(["id", "type"])->keyBy("type"),
        ];
    }

    private function pass6HoursClosure()
    {
        return function ($builder) {
            $now = time();
            $builder->where("microtime", "<=", $now)->where("microtime", ">=", $now - 6 * 3600);
        };
    }

    public function updateBasic(Request $request, ComputeInstance $computeInstance)
    {
        $this->validate($request, [
            "name" => "required|max:32",
            "description" => "nullable|max:255",
        ]);

        $computeInstance->update([
            "name" => $request->name,
            "description" => $request->description,
        ]);

        return ["result" => true];
    }

    public function changeHostname(Request $request, ComputeInstance $computeInstance)
    {
        $this->powerOnValidate($computeInstance);
        $this->validate($request, [
            "hostname" => [
                "required",
                "regex:/^[a-zA-Z0-9_\-\.]{1,15}/"
            ]
        ]);

        return ["result" => true, "operationRequest" => ComputeResourceOperationRequest::newOperationRequestThenDispatch($computeInstance->user_id, ResourceTypeCode::TYPE_COMPUTE_INSTANCE, $computeInstance->id, TypeCode::TYPE_CHANGE_HOSTNAME, ["hostname" => $request->hostname])];
    }

    public function resetOSPassword(Request $request, ComputeInstance $computeInstance)
    {
        $this->powerOnValidate($computeInstance);
        $newPassword = ComputeInstances::generatePassword();
        return ["result" => true, "operationRequest" => ComputeResourceOperationRequest::newOperationRequestThenDispatch($computeInstance->user_id, ResourceTypeCode::TYPE_COMPUTE_INSTANCE, $computeInstance->id, TypeCode::TYPE_CHANGE_OS_PASSWORD, ["password" => $newPassword])];
    }

    public function reconfigureOSNetwork(ComputeInstance $computeInstance)
    {
        $this->powerOnValidate($computeInstance);
        return ["result" => true, "operationRequest" => ComputeResourceOperationRequest::newOperationRequestThenDispatch($computeInstance->user_id, ResourceTypeCode::TYPE_COMPUTE_INSTANCE, $computeInstance->id, TypeCode::TYPE_RECONFIGURE_OS_NETWORK)];
    }

    public function changePackage(Request $request, ComputeInstance $computeInstance, $computeInstancePackageId)
    {
        $this->powerOffValidate($computeInstance);

        /**
         * @var ComputeNode $node
         */
        $node = $computeInstance->node;
        /**
         * @var Zone $zone
         */
        $zone = $node->zone;

        if (!($computeInstancePackage = $zone->hasPackage($computeInstancePackageId))) {
            throw new InsufficientResourceException();
        }

        if ($computeInstance->compute_instance_package_id === intval($computeInstancePackageId)) {
            return ["result" => false, "message" => "实例已在使用所指定的规格"];
        }

        $packageMonthlyPrice = intval($computeInstancePackage->price_per_hour * 720);
        if ($computeInstance->user->credit < $packageMonthlyPrice) {
            throw new MinCreditRequired($packageMonthlyPrice, $computeInstance->user->credit);
        }

        ComputeInstance::massCharge($computeInstance);

        $memoryDiff = $computeInstancePackage->memory - $computeInstance->package->memory;
        $node->allocateMemory($memoryDiff, function () use ($request, $computeInstance, $computeInstancePackageId, $zone) {
            // Increase origin package stock
            ZoneHasComputeInstancePackage::query()
                ->where("zone_id", $zone->id)
                ->where("package_id", $computeInstance->compute_instance_package_id)
                ->whereNotNull("stock")
                ->increment("stock");

            $computeInstance->update([
                "compute_instance_package_id" => $computeInstancePackageId,
            ]);

            // Decrease target package stock
            try {
                ZoneHasComputeInstancePackage::query()
                    ->where("zone_id", $zone->id)
                    ->where("package_id", $computeInstancePackageId)
                    ->whereNotNull("stock")
                    ->decrement("stock");
            } catch (QueryException $e) {
                if (intval($e->getCode()) === 22003) {
                    throw new PackageOutOfStockException();
                }
                throw $e;
            }
        });

        return $this->reconfigure($request, $computeInstance);
    }

    public function changePublicImage(Request $request, ComputeInstance $computeInstance)
    {
        $targetVolume = $computeInstance->attachedLocalVolumes()->where("attach_order", 0)->first();
        if (is_null($targetVolume))
            return ["result" => false, "errno" => GlobalErrorCode::PRIMARY_BOOTABLE_DISK_NOT_FOUND];

        try {
            $image = Image::query()->findOrFail($request->publicImageId);
        } catch (ModelNotFoundException $e) {
            return ["result" => false, "errno" => GlobalErrorCode::PUBLIC_IMAGE_NOT_FOUND];
        }

        $computeInstance->update([
            "use_legacy_bios" => $image->use_legacy_bios,
        ]);
        return ["result" => true, "operationRequest" => ComputeResourceOperationRequest::newOperationRequestThenDispatch($computeInstance->user_id, ResourceTypeCode::TYPE_COMPUTE_INSTANCE, $computeInstance->id, TypeCode::TYPE_CHANGE_PUBLIC_IMAGE, ["imageId" => $request->publicImageId, "volumeId" => $targetVolume->id])];
    }

    public function console(ComputeInstance $computeInstance)
    {
        /**
         * @var ComputeNode $computeNode
         */
        $computeNode = $computeInstance->node;
        $serialNumber = openssl_x509_parse($computeNode->getClientCertificate())["serialNumber"];

        if ($computeInstance->node->no_vnc_host) {
            $host = $computeInstance->node->no_vnc_host;
        } else {
            $host = $computeInstance->node->host;
        }

        if ($computeInstance->node->no_vnc_port) {
            $port = $computeInstance->node->no_vnc_port;
        } else {
            $port = 6080;
        }

        if ($computeInstance->node->no_vnc_client_connect_port) {
            $port = $computeInstance->node->no_vnc_client_connect_port;
        }

        $password = $computeInstance->vnc_password;

        $authentication = Authenticator::generateSign($computeNode->getClientPrivateKey(), $serialNumber, $computeInstance->unique_id);

        return view("ClientArea.console", [
            "host" => $host,
            "port" => $port,
            "password" => $password,
            "id" => $computeInstance->unique_id,
            "salt" => $authentication["salt"],
            "serial" => $serialNumber,
            "expire_at" => $authentication["expireAt"],
            "signature" => $authentication["signature"],
        ]);
    }

    public function reconfigure(Request $request, ComputeInstance $computeInstance)
    {
        $this->powerOffValidate($computeInstance);
        $operationRequest = ComputeInstance\OperationRequest::newRequestThenDispatch($computeInstance, TypeCode::TYPE_RECONFIGURE, [
            "ejectMedias" => boolval($request->ejectMedias),
        ]);

        return ["result" => true, "operationRequest" => $operationRequest];
    }

    public function saveAdvanceSettings(Request $request, ComputeInstance $computeInstance)
    {
        $machineType = intval(boolval($request->machineType));
        $useLegacyBIOS = intval(boolval($request->useLegacyBIOS));

        // Prevent use Q35 if there is any volume attached via IDE
        if (!$machineType && $computeInstance->attachedLocalVolumes()->where("bus", VolumeBusCode::BUS_IDE)->count())
            return ["result" => false, "errno" => GlobalErrorCode::Q35_NOT_SUPPORT_IDE];

        $computeInstance->update([
            "machine_type" => $machineType,
            "use_legacy_bios" => $useLegacyBIOS,
        ]);

        if ($request->saveAndApply) {
            return $this->reconfigure($request, $computeInstance);
        } else {
            return ["result" => true, "machineType" => $machineType, "useLegacyBIOS" => $useLegacyBIOS];
        }
    }

    protected function powerOnValidate(ComputeInstance $computeInstance)
    {
        if ($computeInstance->power_status !== ComputeInstanceStatusCode::POWER_STATUS_RUNNING)
            throw new CCMSAPIException("POWER_ON_INSTANCE_AND_OS_STARTED_IS_REQUIRED", GlobalErrorCode::POWER_ON_INSTANCE_AND_OS_STARTED_IS_REQUIRED);
    }

    protected function powerOffValidate(ComputeInstance $computeInstance)
    {
        if ($computeInstance->power_status !== ComputeInstanceStatusCode::POWER_STATUS_OFF)
            throw new CCMSAPIException("POWER_OFF_INSTANCE_IS_REQUIRED", GlobalErrorCode::POWER_OFF_INSTANCE_IS_REQUIRED);
    }
}
