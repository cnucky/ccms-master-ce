<?php

namespace App\Http\Controllers\ClientAPI;

use App\Constants\AvailableSystemConfigurations;
use App\SystemConfiguration;
use App\TrafficShareGroup;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClientAPIController extends Controller
{
    public function listUserServices(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $request->user();
        return ["result" => true, "computeInstances" => $user->instances, "localVolumes" => $user->localVolumes, "ipv4s" => $user->ipv4s, "ipv6s" => $user->ipv6s];
    }

    public function dashboard(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $request->user();
        return [
            "result" => true,
            "credit" => $user->credit,
            "frozen_credit" => $user->frozen_credit,
            "userQuota" => $user->userQuota,
            "computeInstanceCount" => $user->instances()->count(),
            "localVolumeCount" => $user->localVolumes()->count(),
            "localVolumeTotalCapacity" => $user->localVolumes()->sum("capacity"),
            "ipv4Count" => $user->ipv4s()->where("unbindable", "!=", 0)->count(),
            "ipv6Count" => $user->ipv6s()->where("unbindable", "!=", 0)->count(),
            "trafficShareGroups" => TrafficShareGroup::query()->get()->keyBy("id"),
            "currentMonthChargedAmountGroupByType" => $user->getUserMonthlyChargedAmountGroupByTypeAttribute(date("Y-m-01 00:00:00")),
            "currentMonthTotalChargedAmount" => $user->getCurrentMonthTotalChargedAmountAttribute(),
            "currentMonthTrafficUsages" => $user->getUserCurrentMonthTrafficUsageAttribute(),
            "freeTXTrafficAtEachShareGroup" => $user->getUserFreeTXTrafficAtEachShareGroup(),
            "instanceTotalPrice" => $user->getComputeInstanceTotalPriceAttribute(),
            "localVolumeTotalPrice" => $user->getLocalVolumeTotalPriceAttribute(),
            "ipv4TotalPrice" => $user->getElasticIPv4TotalPriceAttribute(),
            "ipv6TotalPrice" => $user->getElasticIPv6TotalPriceAttribute(),
            "start_lack_of_credit_at" => $user->start_lack_of_credit_at,
            "autoReleaseAfter" => SystemConfiguration::value(AvailableSystemConfigurations::AUTO_RELEASE_LC_USER_RESOURCE_AFTER),
            "inactive" => $user->inactive,
        ];
    }
}
