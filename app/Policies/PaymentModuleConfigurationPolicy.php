<?php

namespace App\Policies;

use App\Admin;
use App\Constants\AdminPermissions;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentModuleConfigurationPolicy
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

    public function operate(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::CRUD_PAYMENT_MODULE_CONFIGURATION);
    }
}
