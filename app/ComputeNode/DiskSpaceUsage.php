<?php

namespace App\ComputeNode;

use Illuminate\Database\Eloquent\Model;
use YunInternet\CCMSCommon\Model\CompositeKey;

class DiskSpaceUsage extends Model
{
    use CompositeKey;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = "compute_node_disk_space_usages";
    
    protected $primaryKey = [
        "compute_node_id",
        "id",
    ];
    
    protected $guarded = [];
}
