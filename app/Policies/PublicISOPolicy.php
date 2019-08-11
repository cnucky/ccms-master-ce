<?php

namespace App\Policies;

use App\Admin;
use App\Constants\AdminPermissions;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PublicISOPolicy
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
        return true;
    }

    public function create(Admin $admin)
    {
        return $admin->hasPermissionTo(AdminPermissions::CU_PUBLIC_ISO);
    }

    public function update(Admin $admin)
    {
        return $admin->hasPermissionTo(AdminPermissions::CU_PUBLIC_ISO);
    }

    public function delete(Admin $admin)
    {
        return $admin->hasPermissionTo(AdminPermissions::D_PUBLIC_ISO);
    }
}
