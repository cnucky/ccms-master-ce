<?php

namespace App;

use App\Constants\AdminPermissions;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User;

class Admin extends User
{
    protected $guard_name = 'admin';

    use Notifiable;

    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        "created_at",
        "updated_at",
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function availablePermissions()
    {
        return Permission::query()->where("guard_name", "admin")->get();
    }
}
