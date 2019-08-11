<?php

namespace App\Policies;

use App\Admin;
use App\Constants\AdminPermissions;
use App\Ticket\Ticket;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function operate(User $user, Ticket $ticket)
    {
        return $user->id === $ticket->user_id;
    }

    public function index(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::R_TICKET);
    }

    public function makeReply(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::MAKE_REPLY_TO_TICKET);
    }

    public function update(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::U_TICKET);
    }

    public function delete(Admin $admin)
    {
        return SuperAdminUtils::hasSuperAdminOrAnyPermissionTo($admin, AdminPermissions::D_TICKET);
    }
}
