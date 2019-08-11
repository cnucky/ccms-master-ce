<?php

namespace App\ComputeNode;

use Illuminate\Database\Eloquent\Model;
use YunInternet\CCMSCommon\Model\CompositeKey;

class BandwidthUsage extends Model
{
    use CompositeKey;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = "compute_node_bandwidth_usages";

    protected $primaryKey = [
        "compute_node_id",
        "id",
        "network_device",
    ];

    protected $guarded = [];
}
