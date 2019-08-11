<?php

namespace App\Http\Controllers\UserQuota;

use App\UserQuota;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserQuotaIndexController extends Controller
{
    public function __construct()
    {
        $this->middleware("can:index," . UserQuota::class);
    }

    public function index()
    {
        return ["result" => true, "userQuotas" => UserQuota::query()->withCount("users")->get()];
    }
}
