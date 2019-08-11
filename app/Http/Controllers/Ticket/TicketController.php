<?php

namespace App\Http\Controllers\Ticket;

use App\Constants\ProductType;
use App\Constants\TicketStatus;
use App\Http\Controllers\ModelControllers\FilterableIndexController;
use App\Module\Exception\ModuleException;
use App\Ticket\Department;
use App\Ticket\Reply;
use App\Ticket\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TicketController extends FilterableIndexController
{
    public function index(Request $request)
    {
        return ["result" => true, "tickets" => $this->paginate($request, Ticket::query()
            ->where("user_id", $request->user()->id)
            ->with("department")
            ->orderBy("status")
            ->orderByDesc("replied_at"))
        ];
    }

    public function show(Request $request, Ticket $ticket)
    {
        return ["result" => true, "ticket" => $ticket, "replies" => $ticket->replies()->orderByDesc("id")->get(), "relativeService" => self::retrieveRelativeService($ticket)];
    }

    public function store(Request $request)
    {
        $values = $this->storeValidateAndRetrieveValues($request);
        DB::beginTransaction();
        try {
            $ticket = Ticket::query()->create($values);
            Reply::query()->create([
                "ticket_id" => $ticket->id,
                "content" => $request->get("content"),
            ]);
            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollback();
            throw $throwable;
        }

        return ["result" => true, "ticket" => $ticket];
    }

    public function makeReply(Request $request, Ticket $ticket)
    {
        $this->validate($request, ["content" => "required|max:65535"]);
        $reply = Reply::query()->create([
            "ticket_id" => $ticket->id,
            "content" => $request->get("content"),
        ]);
        $ticket->update([
            "replied_at" => $reply->created_at,
            "status" => TicketStatus::STATUS_CUSTOMER_REPLIED,
        ]);

        return ["result" => true, "reply" => $reply];
    }

    public function close(Request $request, Ticket $ticket)
    {
        $ticket->update(["status" => TicketStatus::STATUS_CLOSED]);
        return ["result" => true];
    }

    public static function retrieveRelativeService(Ticket $ticket)
    {
        $relativeService = null;
        if (!is_null($ticket->product_type) && $ticket->relative_service_id) {
            $modelClassName = @ProductType::MODEL_MAP[$ticket->product_type];
            if ($modelClassName) {
                $relativeService = call_user_func([$modelClassName, "query"])->where("id", $ticket->relative_service_id)->first();
            }
        }
        return $relativeService;
    }

    private function storeValidateAndRetrieveValues(Request $request)
    {
        $this->validate($request, [
            "department_id" => ["required", Rule::exists("ticket_departments", "id")],
            "priority" => ["required", Rule::in([0, 1, 2])],
            "title" => ["required", "max:255"],
            "product_type" => ["nullable", Rule::in([0, 1, 2, 3])],
            "content" => ["required", "max:65535"],
        ]);

        $values = [
            "department_id" => $request->department_id,
            "user_id" => $request->user()->id,
            "priority" => $request->priority,
            "status" => TicketStatus::STATUS_PENDING,
            "title" => $request->title,
            "replied_at" => date("Y-m-d H:i:s"),
        ];

        $department = Department::query()->findOrFail($request->department_id);

        if (!(is_null($request->product_type) || is_null($request->relative_service_id) || !$department->show_relative_service_select)) {
            try {
                $this->findModelByUserId(call_user_func([ProductType::MODEL_MAP[$request->product_type], "query"]), $request->relative_service_id, $request->user()->id);
            } catch (ModuleException $e) {
                throw ValidationException::withMessages([
                    "show_relative_service_select" => ":attribute 不存在",
                ]);
            }
            $values["product_type"] = $request->product_type;
            $values["relative_service_id"] = $request->relative_service_id;
        }
        return $values;
    }

    private function findModelByUserId(Builder $builder, $id, $userId)
    {

    }
}
