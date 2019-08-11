<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Constants\AdminPermissions;
use App\Constants\GlobalErrorCode;
use App\Constants\StatusCode;
use App\Exceptions\NoSuperAdministratorException;
use App\Policies\SuperAdminUtils;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware("can:" . AdminPermissions::SUPER)->except([
            "availableRoles",
            "show",
            "update"
        ]);
        $this->middleware("can:show,admin")->only(["show"]);
        $this->middleware("can:update,admin")->only(["update"]);
    }

    public function availableRoles()
    {
        return [
            "result" => true,
            "availableRoles" => Role::query()->where("guard_name", "admin")->get(),
        ];
    }

    public function show(Request $request, Admin $admin)
    {
        return [
            "result" => true,
            "adminData" => [
                "admin" => $admin,
                "assignedRole" => $admin->roles()->first(),
                "availableRoles" => Role::query()->where("guard_name", "admin")->get(),
            ]
        ];
    }

    public function store(Request $request)
    {
        $this->storeValidate($request, true);

        DB::transaction(function () use ($request, &$admin) {
            $admin = Admin::query()->create([
                "name" => $request->name,
                "email" => $request->email,
                "phone" => $request->phone,
                "status" => $request->status,
                "password" => bcrypt($request->password),
            ]);
            $admin->syncRoles([Role::query()->findOrFail($request->assigned_role_id)]);
        });

        return ["result" => true, "admin" => $admin];
    }

    public function update(Request $request, Admin $admin)
    {
        $this->storeValidate($request, false, $admin);

        if (SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($request->user("admin"), AdminPermissions::U_SELF_ADMIN)) {
            $values = [
                "name" => $request->name,
                "email" => $request->email,
                "phone" => $request->phone,
            ];
        } else {
            $values = [];
        }

        if ($request->user("admin")->hasPermissionTo(AdminPermissions::SUPER)) {
            DB::transaction(function () use ($request, $admin) {
                $admin->syncRoles([Role::query()->findOrFail($request->assigned_role_id)]);
                if (!Admin::permission(AdminPermissions::SUPER)->count()) {
                    throw new NoSuperAdministratorException();
                }
            });
            $values["status"] = $request->status;
        }

        if ($request->password) {
            $values["password"] = bcrypt($request->password);
        }

        $admin->update($values);

        return $this->show($request, $admin);
    }

    public function destroy(Request $request, Admin $admin)
    {
        if ($request->user("admin")->id === $admin->id) {
            return ["result" => false, "errno" => GlobalErrorCode::CAN_NOT_DELETE_SELF];
        }

        $admin->delete();

        return ["result" => true];
    }

    private function storeValidate(Request $request, $requiredPassword = false, Admin $except = null)
    {
        $emailValidateRule = Rule::unique("admins", "email");
        if (!is_null($except)) {
            $emailValidateRule->ignore($except->id);
        }

        $this->validate($request, [
            "name" => "required|max:32",
            "email" => ["required", "email", "max:64", $emailValidateRule],
            "phone" => "nullable|digits_between:11,16",
            "password" => ($requiredPassword ? "required" : "nullable") . "|min:6|confirmed",
            "status" => ["required", Rule::in([StatusCode::STATUS_NORMAL, StatusCode::STATUS_SUSPENDED])],
            "assigned_role_id" => ["required", Rule::exists("roles", "id")],
        ]);
    }
}
