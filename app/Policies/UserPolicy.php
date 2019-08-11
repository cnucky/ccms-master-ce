<?php

namespace App\Policies;

use App\Admin;
use App\Constants\AdminPermissions;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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

    public function index(Admin $admin)
    {
        return $admin->hasPermissionTo(AdminPermissions::R_USER);
    }

    public function create(Admin $admin)
    {
        return $admin->hasPermissionTo(AdminPermissions::CU_USER);
    }

    public function update(Admin $admin)
    {
        return $admin->hasPermissionTo(AdminPermissions::CU_USER);
    }

    public function addCredit(Admin $admin)
    {
        return $admin->hasPermissionTo(AdminPermissions::ADD_CREDIT_TO_USER);
    }

    public function removeCredit(Admin $admin)
    {
        return $admin->hasPermissionTo(AdminPermissions::REMOVE_CREDIT_FROM_USER);
    }

    public function login(Admin $admin)
    {
        return $admin->hasPermissionTo(AdminPermissions::LOGIN_AS_USER);
    }

    public function suspend(Admin $admin)
    {
        return $admin->hasPermissionTo(AdminPermissions::SUSPEND_USER);
    }

    public function unsuspend(Admin $admin)
    {
        return $admin->hasPermissionTo(AdminPermissions::UNSUSPEND_USER);
    }

    public function delete(Admin $admin)
    {
        return $admin->hasPermissionTo(AdminPermissions::D_USER);
    }
}
