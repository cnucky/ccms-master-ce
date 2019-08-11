<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-22
 * Time: 上午12:02
 */

namespace App\Http\Controllers\IPPool;


use App\IPPool\IPv4;

trait IPPoolIndexMiddleware
{
    public function __construct()
    {
        $this->middleware("can:index," . IPv4::class);
    }
}