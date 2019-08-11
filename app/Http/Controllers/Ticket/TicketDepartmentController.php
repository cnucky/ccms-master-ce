<?php

namespace App\Http\Controllers\Ticket;

use App\Constants\GlobalErrorCode;
use App\Ticket\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class TicketDepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware("can:operate," . Department::class)
            ->except([
                "index",
            ])
        ;
    }

    public function index(Request $request)
    {
        return ["result" => true, "departments" => Department::query()->orderBy("order")->get()];
    }

    public function edit(Request $request, Department $department)
    {
        return ["result" => true, "department" => $department];
    }

    public function store(Request $request)
    {
        $this->storeValidate($request);
        return ["result" => true, "department" => Department::query()->create($this->retrieveValues($request))];
    }

    public function update(Request $request, Department $department)
    {
        $this->storeValidate($request, $department);
        $department->update($this->retrieveValues($request));
        return ["result" => true];
    }

    public function destroy(Department $department)
    {
        $anotherDepartment = Department::query()->where("id", "!=", $department->id)->first();
        if (!$anotherDepartment && $department->tickets()->count()) {
            return ["result" => false, "errno" => GlobalErrorCode::NO_AVAILABLE_DEPARTMENT];
        }
        $department->tickets()->update([
            "department_id" => $anotherDepartment->id,
        ]);
        $department->delete();
        return ["result" => true];
    }

    private function storeValidate(Request $request, Department $except = null)
    {
        $nameValidateRule = Rule::unique("ticket_departments", "name");
        if (!is_null($except))
            $nameValidateRule->ignore($except->id);
        $this->validate($request, [
            "name" => [
                "required",
                "max:32",
                $nameValidateRule
            ],
            "order" => [
                "required",
                "integer",
                "min:-128",
                "max:127",
            ],
            "status" => [
                "required",
                Rule::in([0, 1]),
            ],
            "showRelativeServiceSelect" => [
                "required",
                Rule::in([0, 1]),
            ],
            "description" => [
                "nullable",
                "max:255"
            ],
        ]);
    }

    private function retrieveValues(Request $request)
    {
        return [
            "name" => $request->name,
            "order" => $request->order,
            "status" => $request->status,
            "show_relative_service_select" => $request->showRelativeServiceSelect,
            "description" => $request->description,
        ];
    }
}
