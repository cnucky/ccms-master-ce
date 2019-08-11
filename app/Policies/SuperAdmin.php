<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-21
 * Time: 下午10:34
 */

namespace App\Policies;


use App\Admin;
use App\Constants\AdminPermissions;

trait SuperAdmin
{
    public function before(Admin $admin, $ability)
    {
        if ($admin->hasPermissionTo(AdminPermissions::SUPER)) {
            return true;
        }
        return null;
    }
}