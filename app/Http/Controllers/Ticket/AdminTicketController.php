<?php

namespace App\Http\Controllers\Ticket;

use App\Constants\ProductType;
use App\Constants\TicketStatus;
use App\Http\Controllers\ModelControllers\MassDestroyable;
use App\Ticket\Department;
use App\Ticket\Reply;
use App\Ticket\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class AdminTicketController extends Controller
{
    use MassDestroyable;

    public function show(Request $request, Ticket $ticket)
    {
        $ticket->user;
        $ticket->department;

        return [
            "result" => true,
            "ticket" => $ticket,
            "replies" => $ticket->replies()->orderByDesc("id")->get(),
            "departments" => Department::query()->get(),
            "relativeService" => TicketController::retrieveRelativeService($ticket),
        ];
    }

    public function changeStatus(Request $request, Ticket $ticket)
    {
        $this->validate($request, [
            "status" => [
                "required",
                Rule::in([TicketStatus::STATUS_PENDING, TicketStatus::STATUS_ANSWERED, TicketStatus::STATUS_ON_HOLD, TicketStatus::STATUS_IN_PROGRESS, TicketStatus::STATUS_CLOSED])
            ],
        ]);

        $ticket->update(["status" => $request->status]);

        return ["result" => true];
    }

    public function changeDepartment(Request $request, Ticket $ticket)
    {
        $this->validate($request, [
            "department_id" => [
                "required",
                Rule::exists("ticket_departments", "id")
            ],
        ]);

        $ticket->update(["department_id" => $request->department_id]);

        return ["result" => true];
    }

    public function makeReply(Request $request, Ticket $ticket)
    {
        $this->validate($request, [
            "content" => ["required", "max:65535"],
        ]);

        $reply = Reply::query()->create([
            "ticket_id" => $ticket->id,
            "admin_id" => $request->user("admin")->id,
            "admin_name" => $request->user("admin")->name,
            "content" => $request->get("content"),
        ]);
        $ticket->update([
            "status" => TicketStatus::STATUS_ANSWERED,
            "replied_at" => $reply->created_at,
        ]);

        return ["result" => true, "reply" => $reply];
    }

    public function massClose(Request $request)
    {
        if ($request->has("items") && is_array($items = $request->items)) {
            $deletedItemCount = $this->modelQuery()->whereIn($this->keyColumn(), $items)->update([
                "status" => TicketStatus::STATUS_CLOSED,
            ]);
        } else {
            $deletedItemCount = 0;
            $items = [];
        }

        return ["result" => true, "items" => $items, "deletedItemCount" => $deletedItemCount];
    }

    /**
     * @inheritDoc
     */
    protected function modelQuery()
    {
        return Ticket::query();
    }
}
