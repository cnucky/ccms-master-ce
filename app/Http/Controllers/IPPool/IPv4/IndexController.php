<?php

namespace App\Http\Controllers\IPPool\IPv4;

use App\Http\Controllers\IPPool\IPPoolIndexMiddleware;
use App\Http\Controllers\ModelControllers\FilterableIndexController;
use App\IPPool\IPv4;
use Illuminate\Http\Request;

class IndexController extends FilterableIndexController
{
    use IPPoolIndexMiddleware;

    protected $sortableColumns = [
        "id" => true,
        "human_readable_first_usable_ip" => true,
        "human_readable_last_usable_ip" => true,
        "subnet_network_bits" => true,
        "status" => true,
    ];

    protected $equalSearchColumns = [
        "id",
    ];

    protected $leftMatchSearchColumns = [
        "human_readable_first_usable_ip",
        "human_readable_last_usable_ip",
    ];

    public function __invoke(Request $request)
    {
        return ["result" => true, "pools" => $this->paginate($request, IPv4::query()->withCount("assigned"))];
    }
}
