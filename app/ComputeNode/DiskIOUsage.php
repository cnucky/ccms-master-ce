<?php

namespace App\ComputeNode;

use Illuminate\Database\Eloquent\Model;
use YunInternet\CCMSCommon\Model\CompositeKey;

class DiskIOUsage extends Model
{
    use CompositeKey;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = "compute_node_disk_io_usages";

    protected $primaryKey = [
        "compute_node_id",
        "id",
        "block_device",
    ];

    protected $guarded = [];
}
