<?php

namespace App\Policies;

use App\Admin;
use App\Constants\AdminPermissions;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegionPolicy
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
        return $admin->hasPermissionTo(AdminPermissions::R_REGION);
    }

    public function create(Admin $admin)
    {
        return $admin->hasPermissionTo(AdminPermissions::CU_REGION);
    }

    public function update(Admin $admin)
    {
        return $admin->hasPermissionTo(AdminPermissions::CU_REGION);
    }

    public function delete(Admin $admin)
    {
        return $admin->hasPermissionTo(AdminPermissions::D_REGION);
    }
}
