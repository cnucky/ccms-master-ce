<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ModelControllers\FilterableIndexController;
use App\User;
use App\UserQuota;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserIndexController extends FilterableIndexController
{
    protected $sortableColumns = [
        "id" => true,
        "created_at" => true,
        "status" => true,
    ];

    protected $equalSearchColumns = [
        "id",
        "email",
    ];

    public function __construct()
    {
        $this->middleware("can:index," . User::class);
    }

    public function index(Request $request)
    {
        return ["result" => true, "users" => $this->paginate($request, User::query()
            ->with("userQuota")
            ->withCount("instances")
            ->withCount("localVolumes")
            ->withCount("ipv4s")
            ->withCount("ipv6s")
        )];
    }

    public function indexByUserQuota(Request $request, UserQuota $userQuota)
    {
        return ["result" => true, "users" => $this->paginate($request, User::query()
            ->where("user_quota_id", $userQuota->id)
            ->with("userQuota")
            ->withCount("instances")
            ->withCount("localVolumes")
            ->withCount("ipv4s")
            ->withCount("ipv6s")
        )];
    }
}
