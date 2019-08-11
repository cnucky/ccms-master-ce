<?php

namespace App\Policies;

use App\Admin;
use App\ComputeInstance;
use App\Constants\AdminPermissions;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ComputeInstancePolicy
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

    public function operate(User $user, ComputeInstance $computeInstance)
    {
        return $user->id === $computeInstance->user_id;
    }

    public function index(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::R_COMPUTE_INSTANCE);
    }

    public function adminOperate(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::OPERATE_COMPUTE_INSTANCE);
    }

    public function suspend(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::SUSPEND_COMPUTE_INSTANCE);
    }

    public function unsuspend(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::UNSUSPEND_COMPUTE_INSTANCE);
    }

    public function update(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::U_COMPUTE_INSTANCE);
    }

    public function delete(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::D_COMPUTE_INSTANCE);
    }
}
