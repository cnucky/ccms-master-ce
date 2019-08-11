<?php

namespace App\Http\Controllers\Role;

use App\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Role;

class AdminRoleIndexController extends Controller
{
    public function index()
    {
        return [
            "result" => true,
            "roles" => Role::query()->where("guard_name", "admin")->withCount("admins")->get(),
        ];
    }

    public function availablePermissions()
    {
        return [
            "result" => true,
            "availablePermissions" => Admin::availablePermissions()->pluck("name", "id"),
        ];
    }
}
