<?php

namespace App\Http\Controllers\IPPool;

use App\Http\Controllers\ModelControllers\FilterableIndexController;
use App\IPPool\IPv4;
use App\IPPool\IPv6;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

abstract class AssignmentIndexController extends FilterableIndexController
{
    protected $sortableColumns = [
        "id" => true,
        "position" => true,
        "user_id" => true,
        "nic_id" => true,
        "assigned_at" => true,
    ];

    protected $equalSearchColumns = [
        "id",
    ];

    protected $leftMatchSearchColumns = [
        "human_readable_first_usable",
        "human_readable_last_usable",
    ];

    protected function doIndex(Request $request, Builder $builder)
    {
        return [
            "result" => true,
            "assignments" => $this->paginate($request, $builder->with(["user", "networkInterface.instance", "pool"])->whereNotNull("user_id")),
        ];
    }

    /**
     * @param Request $request
     * @param IPv4|IPv6 $ipPool
     * @return array
     */
    protected function doIndexByIPPool(Request $request, $ipPool)
    {
        return [
            "result" => true,
            "assignments" => $this->paginate($request, $ipPool->assignment()
                ->with(["user", "networkInterface.instance"])
                ->where("pool_id", $ipPool->id)
                ->whereNotNull("user_id")),
        ];
    }

    protected function doIndexByUser(Request $request, User $user, Builder $builder)
    {
        return [
            "result" => true,
            "assignments" => $this->paginate($request, $builder
                ->with(["user", "networkInterface.instance", "pool"])
                ->where("user_id", $user->id)),
        ];
    }
}
