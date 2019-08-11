<?php

namespace App\Http\Controllers\AdminAPI;

use App\ComputeInstance;
use App\Constants\ComputeInstanceStatusCode;
use App\Constants\TicketStatus;
use App\IPPool\IPv4Assignment;
use App\IPPool\IPv6Assignment;
use App\Module\Constants\Payment\TradeRefundStatus;
use App\Node\ComputeNode;
use App\PaymentTradeRefund;
use App\Ticket\Ticket;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function dashboard()
    {
        return [
            "result" => true,
            "userCount" => User::query()->count(),
            "instanceCount" => ComputeInstance::query()->count(),
            "localVolumeCount" => ComputeInstance\LocalVolume::query()->count(),
            "elasticIPv4Count" => IPv4Assignment::query()->where("unbindable", "!=", 0)->whereNotNull("user_id")->count(),
            "elasticIPv6Count" => IPv6Assignment::query()->where("unbindable", "!=", 0)->whereNotNull("user_id")->count(),
            "offlineComputeNodeCount" => ComputeNode::query()->where("last_communicated_at", "<=", date("Y-m-d H:i:s", time() - 600))->count(),
            "awaitingReplyTicketCount" => Ticket::query()->whereIn("status", [TicketStatus::STATUS_PENDING, TicketStatus::STATUS_CUSTOMER_REPLIED, TicketStatus::STATUS_IN_PROGRESS])->count(),
            "pendingRefundTradeCount" => PaymentTradeRefund::query()->where("status", TradeRefundStatus::STATUS_PENDING)->count(),
            "createUnsuccessfullyInstanceCount" => ComputeInstance::query()->where("status", ComputeInstanceStatusCode::STATUS_CREATE_UNSUCCESSFULLY)->count(),
            "destroyUnsuccessfullyInstanceCount" => ComputeInstance::query()->where("status", ComputeInstanceStatusCode::STATUS_DESTROY_UNSUCCESSFULLY)->count(),
        ];
    }
}
