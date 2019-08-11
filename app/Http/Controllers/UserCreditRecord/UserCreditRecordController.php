<?php

namespace App\Http\Controllers\UserCreditRecord;

use App\Constants\CreditRecordType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserCreditRecordController extends Controller
{
    public function dashboard(Request $request)
    {
        $now = date("Y-m-d-H");
        list($year, $month, $day, $hour) = explode("-", $now);

        $yesterday = date("Y-m-d H:i:s",  strtotime(sprintf("%s-%s-%s %s:00:00", $year, $month, $day, $hour)) - 86400);

        $lastMonth = date("Y-m-d H:i:s",  strtotime(sprintf("%s-%s-%s", $year, $month, $day)) - 2592000);
        $lastYear = sprintf("%s-%s", intval($year) - 1, $month);

        $user = $request->user("web");

        return [
            "result" => true,
            "credit" => $user->credit,
            "frozen_credit" => $user->frozen_credit,
            "recentlyChargeHourly" => $this->commonBuilder($request, "%Y-%m-%d %H", $yesterday)->get(),
            "recentlyChargeDaily" => $this->commonBuilder($request, "%Y-%m-%d", $lastMonth)->get(),
            "recentlyChargeMonthly" => $this->commonBuilder($request, "%Y-%m", $lastYear)->get(),
        ];
    }

    private function commonBuilder(Request $request, $dataFormat, $minTime)
    {
        return $request->user()->commonCreditRecordBuilder($dataFormat, $minTime);
    }
}
