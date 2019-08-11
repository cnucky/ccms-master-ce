<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-22
 * Time: 下午8:54
 */

namespace App\Exceptions;


use App\Constants\GlobalErrorCode;
use Illuminate\Contracts\Support\Renderable;

class NoSuperAdministratorException extends \Exception implements Renderable
{
    /**
     * @inheritDoc
     */
    public function render()
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        return ["result" => false, "errno" => GlobalErrorCode::NO_SUPER_ADMINISTRATOR];
    }
}