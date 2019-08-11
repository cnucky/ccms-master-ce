<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-18
 * Time: 下午8:13
 */

namespace App\Constants\Volume\OperationRequest;


use App\Jobs\Volume\Attach;
use App\Jobs\Volume\Detach;
use App\Jobs\Volume\NewLocalVolume;
use App\Jobs\Volume\Release;
use App\Jobs\Volume\Resize;

interface Handler
{
    const HANDLERS = [
        TypeCode::TYPE_RESIZE => Resize::class,
        TypeCode::TYPE_NEW => NewLocalVolume::class,
        TypeCode::TYPE_DETACH => Detach::class,
        TypeCode::TYPE_RELEASE => Release::class,
        TypeCode::TYPE_ATTACH => Attach::class,
    ];
}