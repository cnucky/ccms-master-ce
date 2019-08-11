<?php

namespace App\ComputeInstance;

use Illuminate\Database\Eloquent\Model;
use YunInternet\CCMSCommon\Model\CompositeKey;

class CPUUsage extends Model
{
    use CompositeKey;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = "compute_instance_cpu_usages";

    protected $primaryKey = [
        "compute_instance_id",
        "id",
    ];

    protected $guarded = [];
}
