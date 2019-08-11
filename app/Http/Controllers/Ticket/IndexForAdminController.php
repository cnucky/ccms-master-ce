<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\ModelControllers\FilterableIndexController;
use App\Ticket\Department;
use App\Ticket\Ticket;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class IndexForAdminController extends FilterableIndexController
{
    protected $sortableColumns = [
        "id" => true,
        "user_id" => true,
        "department_id" => true,
        "status" => true,
        "replied_at" => true,
    ];

    protected $equalSearchColumns = [
        "id",
    ];

    protected $fulltextSearchColumns = [
        "title",
    ];

    public function index(Request $request)
    {
        return $this->doIndex($request, $this->makeBasicBuilder($request));
    }

    public function indexByUser(Request $request, User $user)
    {
        return $this->doIndex($request, $this->makeBasicBuilder($request)->where("user_id", $user->id));
    }

    protected function doIndex(Request $request, Builder $builder)
    {
        return ["result" => true, "tickets" => $this->paginate($request, $builder), "departments" => Department::query()->get()];
    }

    protected function makeBasicBuilder(Request $request)
    {
        $builder = Ticket::query()
            ->with("department")
            ->with("user")
        ;

        if (is_null($request->sortKey)) {
            $builder
                ->orderBy("status")
                ->orderByDesc("replied_at")
            ;
        }

        if (!is_null($request->status)) {
            $builder->where("status", $request->status);
        }

        if ($request->department_id) {
            $builder->where("department_id", $request->department_id);
        }

        if (!is_null($request->priority)) {
            $builder->where("priority", $request->priority);
        }

        return $builder;
    }
}
