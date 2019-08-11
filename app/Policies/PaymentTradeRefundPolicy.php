<?php

namespace App\Policies;

use App\Admin;
use App\Constants\AdminPermissions;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentTradeRefundPolicy
{
    use HandlesAuthorization;

    use SuperAdmin;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function commit(Admin $admin)
    {
        return $admin->hasPermissionTo(AdminPermissions::COMMIT_PAYMENT_TRADE_REFUND);
    }

    public function cancel(Admin $admin)
    {
        return $admin->hasPermissionTo(AdminPermissions::CANCEL_PAYMENT_TRADE_REFUND);
    }

    public function changeStatus(Admin $admin)
    {
        return $admin->hasPermissionTo(AdminPermissions::U_STATUS_PAYMENT_TRADE_REFUND);
    }
}
