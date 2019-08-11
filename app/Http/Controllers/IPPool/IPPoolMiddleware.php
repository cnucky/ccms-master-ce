<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-22
 * Time: 上午12:01
 */

namespace App\Http\Controllers\IPPool;


use App\IPPool\IPv4;

trait IPPoolMiddleware
{
    public function __construct()
    {
        $this->middleware("can:index," . IPv4::class)->only([
            "edit",
            "show",
        ]);
        $this->middleware("can:create," . IPv4::class)->only([
            "store",
        ]);
        $this->middleware("can:update," . IPv4::class)->only([
            "update",
        ]);
        $this->middleware("can:delete," . IPv4::class)->only([
            "massDestroy",
            "destroy",
        ]);
    }
}