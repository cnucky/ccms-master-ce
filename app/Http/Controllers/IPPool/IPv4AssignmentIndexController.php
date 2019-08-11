<?php

namespace App\Http\Controllers\IPPool;

use App\IPPool\IPv4;
use App\IPPool\IPv4Assignment;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IPv4AssignmentIndexController extends AssignmentIndexController
{
    public function index(Request $request)
    {
        return $this->doIndex($request, IPv4Assignment::query());
    }

    public function indexByIPPool(Request $request, IPv4 $IPv4)
    {
        return $this->doIndexByIPPool($request, $IPv4);
    }

    public function indexByUser(Request $request, User $user)
    {
        return $this->doIndexByUser($request, $user, IPv4Assignment::query());
    }
}
