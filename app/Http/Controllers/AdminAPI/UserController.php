<?php

namespace App\Http\Controllers\AdminAPI;

use App\Utils\UserInformation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function currentUser(Request $request)
    {
        return UserInformation::adminUserInformation();
    }
}
