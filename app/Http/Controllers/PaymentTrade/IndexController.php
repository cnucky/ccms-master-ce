<?php

namespace App\Http\Controllers\PaymentTrade;

use App\Http\Controllers\ModelControllers\FilterableIndexController;
use App\Module\Constants\Payment\TradeRefundStatus;
use App\Module\Constants\Payment\TradeStatus;
use App\PaymentTrade;
use App\User;
use Illuminate\Http\Request;

class IndexController extends FilterableIndexController
{
    protected $equalSearchColumns = [
        "id",
    ];

    protected $leftMatchSearchColumns = [
        "no",
    ];

    protected $sortableColumns = [
        "id" => true,
        "no" => true,
        "fee_in_default_currency" => true,
        "paid_at" => true,
        "created_at" => true,
        "user_id" => true,
    ];

    public function index(Request $request)
    {
        $tradeQueryBuilder = $this->getVeryBasicQueryBuilder($request)->where("user_id", $request->user()->id);
        return ["result" => true, "paymentTrades" => $this->paginate($request, $tradeQueryBuilder)];
    }

    public function indexForAdmin(Request $request)
    {
        $tradeQueryBuilder = $this->getVeryBasicQueryBuilder($request)->with("user");
        return ["result" => true, "paymentTrades" => $this->paginate($request, $tradeQueryBuilder)];
    }

    public function indexByUser(Request $request, User $user)
    {
        $tradeQueryBuilder = $this->getVeryBasicQueryBuilder($request)->where("user_id", $user->id)->with("user");
        return ["result" => true, "paymentTrades" => $this->paginate($request, $tradeQueryBuilder)];
    }

    private function getVeryBasicQueryBuilder(Request $request)
    {
        $tradeQueryBuilder = PaymentTrade::query()
            ->with("paymentModule:id,name")
            ->withCount(["refunds" => function ($builder) {
                $builder->whereIn("status", [TradeRefundStatus::STATUS_SUCCEED, TradeRefundStatus::STATUS_PENDING]);
            }])
            ;
        if (is_null($request->sortKey))
            $tradeQueryBuilder->orderByDesc("id");
        if ($request->status === "0" || $request->status === "1" || $request->status === "2") {
            $tradeQueryBuilder->where("status", $request->status);
        }
        return $tradeQueryBuilder;
    }
}
