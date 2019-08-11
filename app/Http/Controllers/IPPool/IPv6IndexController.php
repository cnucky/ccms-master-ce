<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-25
 * Time: 下午9:31
 */

namespace App\Http\Controllers\IPPool;


use App\IPPool\IPv6;
use App\IPPool\IPv6Assignment;
use Illuminate\Http\Request;
use YunInternet\CCMSCommon\Constants\NetworkType;

class IPv6IndexController extends IndexController
{
    public function indexPublicByUser(Request $request)
    {
        $publicIPv6PoolList = IPv6::query()->where("type", NetworkType::TYPE_PUBLIC_NETWORK)->get(["id"])->toArray();
        return $this->indexByUserWithBuilder($request, IPv6Assignment::query()->whereIn("pool_id", $publicIPv6PoolList));
    }
}