<?php

namespace App\Policies;

use App\ComputeInstance\Device\NetworkInterface;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NetworkInterfacePolicy
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

    public function operate(User $user, NetworkInterface $network)
    {
        return $user->id === $network->instance->user_id;
    }
}
