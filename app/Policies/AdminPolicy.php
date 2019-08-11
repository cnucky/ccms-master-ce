<?php

namespace App\Policies;

use App\Admin;
use App\Constants\AdminPermissions;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
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

    public function show(Admin $currentAdmin, Admin $targetAdmin)
    {
        return $currentAdmin->id === $targetAdmin->id;
    }

    public function update(Admin $currentAdmin, Admin $targetAdmin)
    {
        return $currentAdmin->id === $targetAdmin->id;
    }
}
