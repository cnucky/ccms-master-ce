<?php
/**
 * Created by PhpStorm.
 * Date: 19-1-16
 * Time: 下午8:15
 */

namespace App\Utils;


use App\Admin;

abstract class UserInformation
{
    public static function adminUserInformation()
    {
        /**
         * @var Admin $user
         */
        $user = request()->user("admin");
        return [
            "user" => $user,
            "permissions" => $user ? $user->getAllPermissions()->pluck("id", "name") : [],
        ];
    }

    public static function userInformation()
    {
        return request()->user();
    }
}