<?php

namespace App\Policies;

use App\Admin;
use App\ComputeInstance\LocalVolume;
use App\Constants\AdminPermissions;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocalVolumePolicy
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

    public function operate(User $user, LocalVolume $localVolume)
    {
        return $user->id === $localVolume->user_id;
    }

    public function index(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::R_LOCAL_VOLUME);
    }

    public function attach(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::ATTACH_LOCAL_VOLUME);
    }

    public function detach(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::DETACH_LOCAL_VOLUME);
    }

    public function release(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::RELEASE_LOCAL_VOLUME);
    }

    public function changeBus(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::CHANGE_BUS_LOCAL_VOLUME);
    }

    public function toggleProtectMode(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::TOGGLE_PROTECT_MODE_LOCAL_VOLUME);
    }

    public function togglePrimaryBootableDisk(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::TOGGLE_PRIMARY_BOOTABLE_DISK_LOCAL_VOLUME);
    }
}
