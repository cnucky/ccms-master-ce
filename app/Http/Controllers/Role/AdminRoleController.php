<?php

namespace App\Http\Controllers\Role;

use App\Admin;
use App\Constants\AdminPermissions;
use App\Exceptions\NoSuperAdministratorException;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class AdminRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware("can:" . AdminPermissions::SUPER);
    }

    public function show(Role $role)
    {
        return ["result" => true, "roleData" => [
            "role" => $role,
            "grantedPermissions" => $role->permissions()->get()->pluck("id", "id"),
            "availablePermissions" => Admin::availablePermissions()->pluck("name", "id"),
        ]];
    }

    public function store(Request $request)
    {
        $this->storeValidate($request);
        $role = Role::query()->create([
            "name" => $request->name,
            "guard_name" => "admin",
        ]);
        $role->syncPermissions($this->retrievePermissions($request));

        return ["result" => true, "role" => $role];
    }

    public function update(Request $request, Role $role)
    {
        $this->storeValidate($request, $role);
        DB::transaction(function () use ($request, $role) {
            $role->update(["name" => $request->name]);
            $role->syncPermissions($this->retrievePermissions($request));
            if (!Admin::permission(AdminPermissions::SUPER)->count()) {
                throw new NoSuperAdministratorException();
            }
        });
        return $this->show($role);
    }

    public function destroy(Role $role)
    {
        DB::transaction(function () use ($role) {
            $role->delete();
            if (!Admin::permission(AdminPermissions::SUPER)->count()) {
                throw new NoSuperAdministratorException();
            }
        });
        return ["result" => true];
    }

    private function storeValidate(Request $request, Role $except = null)
    {
        $nameValidateRule = Rule::unique("roles", "name");
        if (!is_null($except)) {
            $nameValidateRule->ignore($except->id);
        }

        $this->validate($request, [
            "name" => $nameValidateRule,
        ]);
    }

    private function retrievePermissions(Request $request)
    {
        if (!is_array($request->grantedPermissions)) {
            return [];
        }

        $grantedPermissions = [];
        foreach ($request->grantedPermissions as $permissionId => $isGranted) {
            if ($isGranted) {
                $grantedPermissions[] = $permissionId;
            }
        }

        return Permission::query()->whereIn("id", $grantedPermissions)->where("guard_name", "admin")->pluck("name");
    }
}
