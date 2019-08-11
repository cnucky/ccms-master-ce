<?php

namespace App\Http\Controllers\ClientAPI\ComputeInstance;

use App\ComputeInstance;
use App\ComputeInstancePackage;
use App\Constants\ComputeInstance\InstanceTypeCode;
use App\Constants\ComputeInstance\OperationRequest\StatusCode;
use App\Constants\ComputeInstance\OperationRequest\TypeCode;
use App\Constants\ComputeInstanceStatusCode;
use App\Constants\ComputeResourceOperation\ResourceTypeCode;
use App\Constants\GlobalErrorCode;
use App\Exceptions\InsufficientPrivateIPv4Exception;
use App\Exceptions\InsufficientPrivateIPv6Exception;
use App\Exceptions\InsufficientPublicIPv4Exception;
use App\Exceptions\InsufficientPublicIPv6Exception;
use App\Exceptions\InsufficientResourceException;
use App\Exceptions\IsBeingCharged;
use App\Exceptions\MinCreditRequired;
use App\Exceptions\PackageOutOfStockException;
use App\Exceptions\QuotaExceededException;
use App\Http\Controllers\ComputeInstance\StoreValidator;
use App\Node\ComputeNode;
use App\Region;
use App\User;
use App\Utils\ComputeInstance\ComputeInstances;
use App\Utils\Node\ComputeNode\Exception\Exception;
use App\Utils\Volume\Constants as LVConstants;
use App\Zone;
use App\ZoneHasComputeInstancePackage;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use YunInternet\CCMSCommon\Constants\MachineType;
use YunInternet\CCMSCommon\Constants\NetworkInterfaceModelCode;
use YunInternet\CCMSCommon\Constants\NetworkType;
use YunInternet\CCMSCommon\Constants\VolumeBusCode;

class SetupRequestController extends Controller
{
    use StoreValidator;

    public function store(Request $request)
    {
        $this->meteDataValidate($request);
        $this->areaValidate($request);
        $this->imageValidate($request);

        /**
         * @var User $user
         */
        $user = $request->user();
        if (!$user->prepareForExclusiveOperation()) {
            throw new IsBeingCharged();
        }

        $exceptionRenderResult = null;

        try {
            $this->validateUserQuota($request, $user);

            $userId = $user->id;
            $zoneId = $request->selectedZoneId;
            /**
             * @var Zone $zone
             */
            $zone = Zone::query()->findOrFail($zoneId);
            $selectedPackage = ComputeInstancePackage::query()->findOrFail($request->selectedPackageId);

            $this->validateZoneAvailableResources($request, $selectedPackage, $zone);

            $memoryRequirement = $selectedPackage->memory;
            $diskCapacityRequirement = $request->storage_capacity;

            $packageMonthlyPrice = intval($selectedPackage->price_per_hour * 720);
            $diskMonthlyPrice = intval($diskCapacityRequirement * $zone->storage_price_per_hour_per_gib * 720);
            $monthlyPrice = $packageMonthlyPrice + $diskMonthlyPrice;
            if ($user->credit < $monthlyPrice)
                throw new MinCreditRequired($monthlyPrice, $user->credit);

            $count = $request->count;

            for ($i = 0; $i < $count; ++$i) {
                try {
                    $this->setupComputeInstance($request, $zone, $userId, $memoryRequirement, $diskCapacityRequirement, $i + 1);
                } catch (InsufficientResourceException $e) {
                    if ($e instanceof Renderable) {
                        $exceptionRenderResult = $e->render();
                    }
                    break;
                } catch (PackageOutOfStockException $e) {
                    if ($e instanceof Renderable) {
                        $exceptionRenderResult = $e->render();
                    }
                    break;
                }
            }

        } finally {
            $user->completeExclusiveOperation();
        }

        return ["result" => true, "requestCount" => $count, "actuallyCreated" => $i, "exceptionRenderResult" => $exceptionRenderResult];
    }

    private function validateUserQuota(Request $request, User $user)
    {
        $userQuota = $user->userQuota;
        $count = $request->count;
        if (!is_null($userQuota->max_instance)) {
            $availableInstance = $userQuota->max_instance - $user->instances()->count();
            if ($availableInstance < $count)
                throw new QuotaExceededException();
        }
        if (!is_null($userQuota->max_storage_capacity_in_gib_unit)) {
            $availableDiskCapacity = $userQuota->max_storage_capacity_in_gib_unit - $user->localVolumes()->sum("capacity");
            $totalStorageCapacityRequirement = $request->storage_capacity * $count;
            if ($availableDiskCapacity < $totalStorageCapacityRequirement)
                throw new QuotaExceededException();
        }
    }

    private function validateZoneAvailableResources(Request $request, ComputeInstancePackage $computeInstancePackage, Zone $zone)
    {
        $count = $request->count;
        if (!$zone->hasPackage($computeInstancePackage->id)) {
            throw new InsufficientResourceException();
        }

        $zoneResourceCounters = $zone->getResourceCountersAttribute();
        $zoneAvailableMemory = $zoneResourceCounters->zone_total_memory_capacity - $zoneResourceCounters->zone_total_allocated_memory_capacity;
        $zoneAvailableDiskCapacity = $zoneResourceCounters->zone_total_disk_capacity - $zoneResourceCounters->zone_total_allocated_disk_capacity;

        $totalMemoryRequirement = $count * $computeInstancePackage->memory;
        $totalDiskCapacityRequirement = $count * $request->storage_capacity;

        if ($zoneAvailableMemory < $totalMemoryRequirement || $zoneAvailableDiskCapacity < $totalDiskCapacityRequirement) {
            throw new InsufficientResourceException();
        }
    }

    private function setupComputeInstance(Request $request, Zone $zone, int $userId, int $memoryRequirement, int $diskCapacityRequirement, int $order)
    {
        $zone->assignNode($memoryRequirement, $diskCapacityRequirement, function ($nodeId) use ($request, $zone, $userId, $order, &$computeInstance) {
            $uniqueId = ComputeInstances::generateUniqueId($userId) . $order;

            $computeInstance = ComputeInstance::query()->create([
                "name" => $request->name . ($order > 1 ? "-" . $order : ""),
                "unique_id" => ComputeInstances::UNIQUE_ID_PREFIX . $uniqueId,
                "user_id" => $userId,
                "compute_node_id" => $nodeId,
                "compute_instance_package_id" => $request->selectedPackageId,
                "type" => InstanceTypeCode::CHARGED_BY_CCMS,
                "hostname" => $request->hostname . ($order > 1 ? "-" . $order : ""),
                "description" => $request->description,
                "machine_type" => MachineType::TYPE_Q35,
                "password" => ComputeInstances::generatePassword(),
                "vnc_password" => ComputeInstances::generateVNCPassword($request->password),
                "status" => ComputeInstanceStatusCode::STATUS_CREATING,
            ]);

            $volumeValues = [
                "user_id" => $userId,
                "unique_id" => LVConstants::UNIQUE_ID_PREFIX . $uniqueId,
                "protected" => 1,
                "compute_node_id" => $nodeId,
                "description" => "System created primary volume",
                "capacity" => $request->storage_capacity,
                "attached_compute_instance_id" => $computeInstance->id,
                "attach_order" => 0,
                "bus" => VolumeBusCode::BUS_VIRTIO,
                "allow_detach" => 1,
                "status" => LVConstants::STATUS_CREATING,
            ];

            if ($request->imageType != "2") {
                $volumeValues["image_id"] = $request->selectedImageId;
            }

            ComputeInstance\LocalVolume::query()->create($volumeValues);

            ComputeInstance\Device\CDROM::query()->create([
                "compute_instance_id" => $computeInstance->id,
                "allow_detach" => 0,
                "allow_eject" => 1,
            ]);
            ComputeInstance\Device\CDROM::query()->create([
                "compute_instance_id" => $computeInstance->id,
                "allow_detach" => 0,
                "allow_eject" => 1,
            ]);

            ComputeInstance\Device\Floppy::query()->create([
                "compute_instance_id" => $computeInstance->id,
                "allow_detach" => 0,
                "allow_eject" => 1,
            ]);
            ComputeInstance\Device\Floppy::query()->create([
                "compute_instance_id" => $computeInstance->id,
                "allow_detach" => 0,
                "allow_eject" => 1,
            ]);

            ComputeInstance\Device\NetworkInterface::query()->create([
                "compute_instance_id" => $computeInstance->id,
                "description" => "System created public network interface",
                "type" => NetworkType::TYPE_PUBLIC_NETWORK,
                "model" => NetworkInterfaceModelCode::MODEL_VIRTIO,
            ]);

            ComputeInstance\Device\NetworkInterface::query()->create([
                "compute_instance_id" => $computeInstance->id,
                "description" => "System created private network interface",
                "type" => NetworkType::TYPE_PRIVATE_NETWORK,
                "model" => NetworkInterfaceModelCode::MODEL_VIRTIO,
            ]);
        });

        /**
         * @var ComputeInstance $computeInstance
         */
        try {
            $this->assignIPForComputeInstance($computeInstance);

            /**
             * @var ComputeInstance\OperationRequest $computeInstanceSetupRequest
             */
            $computeInstanceSetupRequest = ComputeInstance\OperationRequest::query()->create([
                "user_id" => $userId,
                "resource_type" => ResourceTypeCode::TYPE_COMPUTE_INSTANCE,
                "type" => TypeCode::TYPE_SETUP,
                "resource_id" => $computeInstance->id,
                "operation_status" => StatusCode::STATUS_WAIT_FOR_SUBMITTING_TO_NODE,
            ]);

            try {
                ZoneHasComputeInstancePackage::query()
                    ->where("zone_id", $zone->id)
                    ->where("package_id", $request->selectedPackageId)
                    ->whereNotNull("stock")
                    ->decrement("stock");
            } catch (QueryException $e) {
                if (intval($e->getCode()) === 22003) {
                    throw new PackageOutOfStockException();
                }
                throw $e;
            }

            $computeInstanceSetupRequest->dispatch();
        } catch (\Throwable $e) {
            /**
             * @var ComputeInstance\Device\NetworkInterface $networkInterface
             */
            foreach ($computeInstance->networkInterfaces as $networkInterface) {
                $networkInterface->releaseIPAddresses();
            }
            foreach ($computeInstance->attachedLocalVolumes as $attachedLocalVolume) {
                $attachedLocalVolume->deleteWithNodeCounterUpdate();
            }
            $computeInstance->deleteWithNodeCounterUpdate();
            throw $e;
        }
    }

    private function assignIPForComputeInstance(ComputeInstance $computeInstance)
    {
        $computeNode = $computeInstance->node;

        DB::transaction(function () use ($computeInstance, $computeNode) {
            $instanceSize = $computeInstance->instanceSize();
            $userId = $computeInstance->user_id;
            /**
             * @var ComputeInstance\Device\NetworkInterface[] $networkInterfaces
             */
            $networkInterfaces = $computeInstance->networkInterfaces;
            $publicNetworkInterface = $networkInterfaces[0];
            $privateNetworkInterface = $networkInterfaces[1];

            // Get IP address requirements
            $ipv4Block = $instanceSize["public_ipv4"];
            $ipv4BlockSize = $instanceSize["public_ipv4_block_size"];
            if (is_null($ipv4BlockSize))
                $ipv4BlockSize = 32;
            $ipv6Block = $instanceSize["public_ipv6"];
            $ipv6BlockSize = $instanceSize["public_ipv6_block_size"];
            if (is_null($ipv6BlockSize))
                $ipv6BlockSize = 128;

            // Trying to assign IPv4 addresses
            if ($ipv4Block !== 0 && $publicNetworkInterface->ipv4Addresses()->count() !== $ipv4Block) {
                // Release
                $publicNetworkInterface->releaseIPv4Addresses();

                if ($this->assignIPFromIPPool($userId, $computeNode->ipv4Pools(), \App\Constants\IPPool\TypeCode::TYPE_PUBLIC_NETWORK, $ipv4Block, $ipv4BlockSize, $publicNetworkInterface->id) === false) {
                    // If tried to assigned from node unsuccessfully, try zone
                    if ($this->assignIPFromIPPool($userId, $computeNode->zone->ipv4Pools(), \App\Constants\IPPool\TypeCode::TYPE_PUBLIC_NETWORK, $ipv4Block, $ipv4BlockSize, $publicNetworkInterface->id) === false)
                        throw new InsufficientPublicIPv4Exception();
                }
            }

            if ($ipv6Block !== 0 && $publicNetworkInterface->ipv6Addresses()->count() !== $ipv6Block) {
                // Release
                $publicNetworkInterface->releaseIPv6Addresses();

                if ($this->assignIPFromIPPool($userId, $computeNode->ipv6Pools(), \App\Constants\IPPool\TypeCode::TYPE_PUBLIC_NETWORK, $ipv6Block, $ipv6BlockSize, $publicNetworkInterface->id) === false) {
                    if ($this->assignIPFromIPPool($userId, $computeNode->zone->ipv6Pools(), \App\Constants\IPPool\TypeCode::TYPE_PUBLIC_NETWORK, $ipv6Block, $ipv6BlockSize, $publicNetworkInterface->id) === false)
                        throw new InsufficientPublicIPv6Exception();
                }
            }

            $privateNetworkInterface->releaseIPv4Addresses();
            if ($this->assignIPFromIPPool($userId, $computeNode->ipv4Pools(), \App\Constants\IPPool\TypeCode::TYPE_PRIVATE_NETWORK, 1, null, $privateNetworkInterface->id) === false) {
                // If tried to assigned from node unsuccessfully, try zone
                if ($this->assignIPFromIPPool($userId, $computeNode->zone->ipv4Pools(), \App\Constants\IPPool\TypeCode::TYPE_PRIVATE_NETWORK, 1, null, $privateNetworkInterface->id) === false)
                    throw new InsufficientPrivateIPv4Exception();
            }

            $privateNetworkInterface->releaseIPv6Addresses();
            if ($this->assignIPFromIPPool($userId, $computeNode->ipv6Pools(), \App\Constants\IPPool\TypeCode::TYPE_PRIVATE_NETWORK, 1, null, $privateNetworkInterface->id) === false) {
                // If tried to assigned from node unsuccessfully, try zone
                if ($this->assignIPFromIPPool($userId, $computeNode->zone->ipv6Pools(), \App\Constants\IPPool\TypeCode::TYPE_PRIVATE_NETWORK, 1, null, $privateNetworkInterface->id) === false)
                    throw new InsufficientPrivateIPv6Exception();
            }
        });
    }

    /**
     * @param integer $userId
     * @param Builder $ipPoolBuilder
     * @param $type
     * @param $amount
     * @param int|null $blockSize
     * @param $nicId
     * @return bool
     */
    private function assignIPFromIPPool($userId, $ipPoolBuilder, $type, $amount, $blockSize, $nicId)
    {
        $assignResult = false;
        $where = [
            "type" => $type,
            "assign_for_new_instance" => 1,
        ];

        if (!is_null($blockSize))
            $where["subnet_network_bits"] = $blockSize;

        foreach ($ipPoolBuilder->where($where)->get() as $ipPool) {
            $assignResult = $ipPool->assignWithAutoPrepare($userId, $nicId, $amount);
            if ($assignResult)
                break;
        }

        return $assignResult;
    }
}
