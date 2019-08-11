<?php

namespace App\Http\Controllers\IPPool;

use App\Http\Controllers\ModelControllers\FilterableIndexController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends FilterableIndexController
{
    protected $sortableColumns;

    protected $leftMatchSearchColumns = [
        "human_readable_first_usable",
        "human_readable_last_usable"
    ];

    public function __construct()
    {
        $this->sortableColumns = [
            "human_readable_first_usable" => [$this, "sortIP"],
            "nic_id" => true,
            "assigned_at" => true,
        ];
    }

    protected function indexByUserWithBuilder(Request $request, Builder $builder)
    {
        return ["result" => true, "ipAddresses" => $this->paginate($request, $builder->where("user_id", $request->user()->id)->with("networkInterface:id,compute_instance_id", "networkInterface.instance", "pool:id,network_bits,human_readable_gateway,price_per_hour"))];
    }

    public function sortIP($query, $sortKey, $isAsc)
    {
        $direction = $isAsc ? "asc" : "desc";
        $query
            ->orderBy("pool_id")
            ->orderBy("position", $direction)
        ;
    }
}
