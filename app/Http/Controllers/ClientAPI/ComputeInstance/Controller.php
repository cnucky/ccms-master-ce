<?php

namespace App\Http\Controllers\ClientAPI\ComputeInstance;

use App\ComputeInstance;
use App\ComputeInstancePackage;
use App\ComputeInstancePackageCategory;
use App\Constants\CommonStatusCode;
use App\Constants\ComputeInstance\OperationRequest\StatusCode;
use App\Image;
use App\ImageCategory;
use App\Node\ComputeNode;
use App\Region;
use App\Zone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use YunInternet\CCMSCommon\Constants\NetworkType;

class Controller extends \App\Http\Controllers\Controller
{
    /*
    public function __construct()
    {
        $this
            ->middleware('can:operate,computeInstance')
            ->except([
                "createOptions",
            ])
        ;
    }
    */

    public function createOptions()
    {
        return [
            "result" => true,
            "availableRegions" => Region::query()
                ->with(["zones" => function ($builder) {
                    $builder->whereHas("computeNodes", function ($builder) {
                        ComputeNode::whereOnlineAndAvailable($builder);
                    });
                }])
                ->whereHas("computeNodes", function ($builder) {
                    ComputeNode::whereOnlineAndAvailable($builder);
                })
                ->get(),
            // "zoneAvailablePackages" => [],
            "zoneAvailableImages" => ImageCategory::query()
                ->where("status", CommonStatusCode::AVAILABLE)
                ->with(["images" => function ($builder) {
                    $builder
                        ->where("status", CommonStatusCode::AVAILABLE)
                        ->orderByDesc("order")
                    ;
            }])->orderByDesc("order")->get(),
        ];
    }

    public function packageIndexByZone(Zone $zone)
    {
        $availablePackageIdList = $zone->packages()->get()->pluck("id")->toArray();

        return [
            "result" => true,
            "zoneResourceCounters" => $zone->getResourceCountersAttribute(),
            "packageCategories" => ComputeInstancePackageCategory::query()
                ->with(["packages" => function ($builder) use (&$availablePackageIdList) {
                    $builder->whereIn("id", $availablePackageIdList);
                }, "packages.stocks" => function ($builder) use ($zone) {
                    $builder->where("zone_id", $zone->id);
                }])
                ->get(),
        ];
    }

    public function show(ComputeInstance $computeInstance)
    {
        $computeInstance = $this
            ->getComputeInstance($computeInstance->id)
            ->first()
        ;

        return ["result" => true, "instance" => $computeInstance
            ->append(["client_instance_size", "processing_operation_requests"])
        ];
    }

    public function showForAdmin(ComputeInstance $computeInstance)
    {
        $computeInstance = $this
            ->getComputeInstance($computeInstance->id)
            ->first()
        ;

        $computeInstance->makeVisible([
            "vnc_password",
            "override_vCPU",
            "override_memory",
            "override_public_ipv4",
            "override_public_ipv4_block_size",
            "override_public_ipv6",
            "override_public_ipv6_block_size",
            "override_traffic",
            "override_inbound_traffic",
            "override_outbound_traffic",
            "override_inbound_bandwidth",
            "override_outbound_bandwidth",
            "override_io_weight",
            "override_read_bytes_sec",
            "override_write_bytes_sec",
            "override_read_iops_sec",
            "override_write_iops_sec",
        ]);

        return ["result" => true, "instance" => $computeInstance->append(["client_instance_size", "processing_operation_requests"])];
    }

    public function password(ComputeInstance $computeInstance)
    {
        return ["result" => true, "password" => $computeInstance->password];
    }

    private function getComputeInstance($id)
    {
        return ComputeInstance::query()->where("id", $id)->with([
            "cdroms",
            "floppies",
            "node:id,name,zone_id",
            "node.zone:id,name,region_id,traffic_share_group_id,storage_price_per_hour_per_gib",
            "node.zone.region:id,name,icon_class",
            "node.zone.trafficShareGroup",
            "cdroms.media",
            "cdroms.media.category",
            "floppies.media",
            "floppies.media.category",
            "processingOperationRequests",
            "package",
            "package.category",
            "networkInterfaces" => function ($builder) {
                $builder
                    ->where("type", NetworkType::TYPE_PUBLIC_NETWORK)
                ;
            },
            "networkInterfaces.ipv4Addresses" => function ($builder) {
                $builder
                    ->limit(1)
                ;
            },
            "networkInterfaces.ipv6Addresses" => function ($builder) {
                $builder
                    ->limit(1)
                ;
            }

        ]);
    }
}
