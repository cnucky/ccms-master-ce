<?php

namespace App\Policies;

use App\Admin;
use App\Constants\AdminPermissions;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketDepartmentPolicy
{
    use HandlesAuthorization;

    use SuperAdmin;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function operate(Admin $admin)
    {
        return $admin->hasPermissionTo(AdminPermissions::CUD_TICKET_DEPARTMENT);
    }
}
