<?php

namespace App\ComputeNode;

use Illuminate\Database\Eloquent\Model;
use YunInternet\CCMSCommon\Model\CompositeKey;

class MemoryUsage extends Model
{
    use CompositeKey;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = "compute_node_memory_usages";

    protected $primaryKey = [
        "compute_node_id",
        "id",
    ];

    protected $guarded = [];
}
