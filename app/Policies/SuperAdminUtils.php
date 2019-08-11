<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-22
 * Time: 下午12:18
 */

namespace App\Policies;


use App\Admin;
use App\Constants\AdminPermissions;

abstract class SuperAdminUtils
{
    public static function isSuperAdmin(Admin $admin)
    {
        return $admin->hasPermissionTo(AdminPermissions::SUPER);
    }

    public static function hasSuperAdminOrAnyPermissionTo(Admin $admin, $permissions)
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }
        $permissions[] = AdminPermissions::SUPER;
        return $admin->hasAnyPermission($permissions);
    }
}