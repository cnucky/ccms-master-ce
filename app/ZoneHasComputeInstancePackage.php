<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use YunInternet\CCMSCommon\Model\CompositeKey;

class ZoneHasComputeInstancePackage extends Model
{
    public $timestamps = false;

    protected $guarded = ["id"];
}
