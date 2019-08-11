<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-22
 * Time: 上午12:06
 */

namespace App\Http\Controllers\IPPool;


use App\IPPool\IPv4;

trait IPPoolUpdateMiddleware
{
    public function __construct()
    {
        $this->middleware("can:update," . IPv4::class);
    }
}