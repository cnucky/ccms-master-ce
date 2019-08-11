<?php

namespace App\ComputeInstance;

use Illuminate\Database\Eloquent\Model;
use YunInternet\CCMSCommon\Model\CompositeKey;

class BandwidthUsage extends Model
{
    use CompositeKey;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = "compute_instance_bandwidth_usages";

    protected $primaryKey = [
        "network_interface_id",
        "id",
    ];

    protected $guarded = [];
}
