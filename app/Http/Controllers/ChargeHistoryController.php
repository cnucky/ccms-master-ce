<?php

namespace App\Http\Controllers;

use App\ChargeHistory\ComputeInstancePackageChargeHistory;
use App\ChargeHistory\ComputeNodeChargeHistory;
use App\ChargeHistory\IPv4PoolChargeHistory;
use App\ChargeHistory\IPv6PoolChargeHistory;
use App\ChargeHistory\TrafficShareGroupChargeHistory;
use App\ComputeInstancePackage;
use App\Constants\AdminPermissions;
use App\Constants\CreditRecordType;
use App\IPPool\IPv4;
use App\IPPool\IPv6;
use App\Node\ComputeNode;
use App\TrafficShareGroup;
use App\Utils\Time;
use App\Zone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use YunInternet\CCMSCommon\Constants\NetworkType;

class ChargeHistoryController extends Controller
{
    public function byComputeInstancePackages(Request $request)
    {
        return [
            "result" => true,
            "computeInstancePackages" => self::withChargeHistorySum($request, ComputeInstancePackage::query(), $start, $end)
                ->with("category")
                ->orderByDesc("charge_histories_sum")
                ->get(),
            "deleted" => self::deletedSum(ComputeInstancePackageChargeHistory::query(), "compute_instance_package_id", $start, $end),
            "start" => $start,
            "end" => $end,
        ];
    }

    public function byComputeNodes(Request $request)
    {
        return [
            "result" => true,
            "models" => ComputeNode::query()
                ->withCount([
                    "chargeHistories AS instance_charge_histories_sum" => self::chargeSum($request, $start, $end, function ($builder) {
                        $builder->where("type", CreditRecordType::TYPE_CHARGE_COMPUTE_INSTANCE);
                    }),
                    "chargeHistories AS volume_charge_histories_sum" => self::chargeSum($request, $start, $end, function ($builder) {
                        $builder->where("type", CreditRecordType::TYPE_CHARGE_LOCAL_VOLUME);
                    }),
                    "chargeHistories AS charge_histories_sum" => self::chargeSum($request, $start, $end),
                ])
                ->with(["zone.region"])
                ->orderByDesc("charge_histories_sum")
                ->get(),
            "deleted" => [
                "instance_charge_histories_sum" => self::deletedSum(ComputeNodeChargeHistory::query(), "compute_node_id", $start, $end, [
                    "type" => CreditRecordType::TYPE_CHARGE_COMPUTE_INSTANCE,
                ]),
                "volume_charge_histories_sum" => self::deletedSum(ComputeNodeChargeHistory::query(), "compute_node_id", $start, $end, [
                    "type" => CreditRecordType::TYPE_CHARGE_LOCAL_VOLUME,
                ]),
                "charge_histories_sum" => self::deletedSum(ComputeNodeChargeHistory::query(), "compute_node_id", $start, $end),
            ],
            "start" => $start,
            "end" => $end,
        ];
    }

    public function byZone(Request $request)
    {
        return [
            "result" => true,
            "models" => Zone::query()
                ->withCount([
                    "chargeHistories AS instance_charge_histories_sum" => self::chargeSum($request, $start, $end, function ($builder) {
                        $builder->where("type", CreditRecordType::TYPE_CHARGE_COMPUTE_INSTANCE);
                    }, "compute_node_charge_histories."),
                    "chargeHistories AS volume_charge_histories_sum" => self::chargeSum($request, $start, $end, function ($builder) {
                        $builder->where("type", CreditRecordType::TYPE_CHARGE_LOCAL_VOLUME);
                    }, "compute_node_charge_histories."),
                    "chargeHistories AS charge_histories_sum" => self::chargeSum($request, $start, $end, null, "compute_node_charge_histories."),
                ], "compute_node_charge_histories.")
                ->with(["region"])
                ->orderByDesc("charge_histories_sum")
                ->get(),
            "start" => $start,
            "end" => $end,
        ];
    }

    public function byIPv4Pools(Request $request)
    {
        return [
            "result" => true,
            "models" => self::withChargeHistorySum($request, IPv4::query(), $start, $end)
                ->where("type", NetworkType::TYPE_PUBLIC_NETWORK)
                ->orderByDesc("charge_histories_sum")
                ->get(),
            "deleted" => self::deletedSum(IPv4PoolChargeHistory::query(), "pool_id", $start, $end),
            "start" => $start,
            "end" => $end,
        ];
    }

    public function byIPv6Pools(Request $request)
    {
        return [
            "result" => true,
            "models" => self::withChargeHistorySum($request, IPv6::query(), $start, $end)
                ->where("type", NetworkType::TYPE_PUBLIC_NETWORK)
                ->orderByDesc("charge_histories_sum")
                ->get(),
            "deleted" => self::deletedSum(IPv6PoolChargeHistory::query(), "pool_id", $start, $end),
            "start" => $start,
            "end" => $end,
        ];
    }

    public function byTrafficShareGroups(Request $request)
    {
        return [
            "result" => true,
            "models" => self::withChargeHistorySum($request, TrafficShareGroup::query(), $start, $end)
                ->orderByDesc("charge_histories_sum")
                ->get(),
            "deleted" => self::deletedSum(TrafficShareGroupChargeHistory::query(), "traffic_share_group_id", $start, $end),
            "start" => $start,
            "end" => $end,
        ];
    }

    private static function deletedSum(Builder $builder, $column, $start, $end, $extraWhere = null)
    {
        $builder
            ->whereNull($column)
            ->where("created_at", ">=", $start)
            ->where("created_at", "<", $end)
        ;
        if (is_callable($extraWhere) || is_array($extraWhere)) {
            $builder->where($extraWhere);
        }
        return $builder->sum("amount");
    }

    private static function withChargeHistorySum(Request $request, Builder $builder, &$start, &$end, $extraWhere = null) : Builder
    {
        return $builder->withCount(["chargeHistories AS charge_histories_sum" => self::chargeSum($request, $start, $end, $extraWhere)]);
    }

    private static function chargeSum(Request $request, &$start, &$end, $extraWhere = null, $createdAtPrefix = "")
    {
        $dayRangeClosure = self::whereDayRange($request, $start, $end, $createdAtPrefix);
        return function ($builder) use ($dayRangeClosure, $extraWhere) {
            $builder
                ->select(DB::raw("IFNULL(SUM(amount), 0) as amount_sum"))
                ->where($dayRangeClosure)
            ;
            if (is_callable($extraWhere) || is_array($extraWhere)) {
                $builder->where($extraWhere);
            }
        };
    }

    private static function whereDayRange(Request $request, &$start, &$end, $createdAtPrefix = "")
    {
        if (empty($start) || empty($end)) {
            Time::retrieveDayRange($request, $start, $end);
        }
        return function ($builder) use ($start, $end, $createdAtPrefix) {
            $builder
                ->where($createdAtPrefix . "created_at", ">=", $start)
                ->where($createdAtPrefix . "created_at", "<", $end)
            ;
        };
    }
}
