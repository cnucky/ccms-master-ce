<?php

namespace App\Policies;

use App\Admin;
use App\Constants\AdminPermissions;
use App\PaymentTrade;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentTradePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function operate(User $user, PaymentTrade $paymentTrade)
    {
        return $user->id === $paymentTrade->user_id;
    }

    public function index(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::R_PAYMENT_TRADE);
    }

    public function markSuccess(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::MARK_SUCCESS_PAYMENT_TRADE);
    }

    public function markCancelled(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::MARK_CANCELLED_PAYMENT_TRADE);
    }

    public function refund(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::REFUND_PAYMENT_TRADE);
    }

    public function update(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::U_PAYMENT_TRADE);
    }
}
