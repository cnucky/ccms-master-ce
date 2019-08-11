<?php

namespace App\Http\Controllers\IPPool;

use App\IPPool\IPv4;
use App\IPPool\IPv4Assignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use YunInternet\CCMSCommon\Constants\NetworkType;

class IPv4IndexController extends IndexController
{
    public function indexPublicByUser(Request $request)
    {
        $publicIPv4PoolList = IPv4::query()->where("type", NetworkType::TYPE_PUBLIC_NETWORK)->get(["id"])->toArray();
        return $this->indexByUserWithBuilder($request, IPv4Assignment::query()->whereIn("pool_id", $publicIPv4PoolList));
    }
}
