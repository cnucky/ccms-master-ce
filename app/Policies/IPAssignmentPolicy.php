<?php

namespace App\Policies;

use App\Admin;
use App\Constants\AdminPermissions;
use App\IPPool\IPv4Assignment;
use App\IPPool\IPv6Assignment;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IPAssignmentPolicy
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

    /**
     * @param User $user
     * @param IPv4Assignment|IPv6Assignment $ipAssignment
     * @return mixed
     */
    public function operate(User $user, $ipAssignment)
    {
        return $user->id === $ipAssignment->user_id;
    }

    public function index(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::R_IP_ASSIGNMENT);
    }

    public function convert(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::CONVERT_IP_ASSIGNMENT);
    }

    public function bind(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::BIND_IP_ASSIGNMENT);
    }

    public function unbind(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::UNBIND_IP_ASSIGNMENT);
    }

    public function allocate(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::ALLOCATE_IP_ASSIGNMENT);
    }

    public function release(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::RELEASE_IP_ASSIGNMENT);
    }
}
