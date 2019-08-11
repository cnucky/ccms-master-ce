<?php

namespace App\Http\Controllers\RefundTrade;

use App\Http\Controllers\ModelControllers\FilterableIndexController;
use App\PaymentTradeRefund;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RefundTradeIndexController extends FilterableIndexController
{
    protected $sortableColumns = [
        "id" => true,
        "refunded_at" => true,
        "created_at" => true,
    ];

    protected $equalSearchColumns = [
        "id",
        "transaction_id",
    ];

    public function index(Request $request)
    {
        $queryBuilder = PaymentTradeRefund::query()
            ->with([
                "trade:id,user_id",
                "trade.user" => function ($builder) {
                    $builder->select("id", "name", "email");
                },
            ])
        ;
        if (is_numeric($request->status)) {
            $queryBuilder->where("status", $request->status);
        }
        return ["result" => true, "refundTrades" => $this->paginate($request, $queryBuilder)];
    }
}
