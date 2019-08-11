<?php

namespace App\Http\Controllers\IPPool;

use App\IPPool\IPv6;
use App\IPPool\IPv6Assignment;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IPv6AssignmentIndexController extends AssignmentIndexController
{
    public function index(Request $request)
    {
        return $this->doIndex($request, IPv6Assignment::query());
    }

    public function indexByIPPool(Request $request, IPv6 $IPv6)
    {
        return $this->doIndexByIPPool($request, $IPv6);
    }

    public function indexByUser(Request $request, User $user)
    {
        return $this->doIndexByUser($request, $user, IPv6Assignment::query());
    }
}
