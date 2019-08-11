<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Constants\AdminPermissions;
use App\Http\Controllers\ModelControllers\FilterableIndexController;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminIndexController extends FilterableIndexController
{
    protected $sortableColumns = [
        "id" => true,
        "email" => true,
        "created_at" => true,
    ];

    protected $equalSearchColumns = [
        "id",
        "email"
    ];

    public function __construct()
    {
        $this->middleware("can:" . AdminPermissions::SUPER);
    }

    public function index(Request $request)
    {
        return ["result" => true, "admins" => $this->paginate($request, Admin::query()->with("roles"))];
    }

    public function indexByRole(Request $request, Role $role)
    {
        return ["result" => true, "admins" => $this->paginate($request, $role->admins())];
    }
}
