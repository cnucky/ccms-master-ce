<?php

namespace App\ComputeInstance;

use Illuminate\Database\Eloquent\Model;
use YunInternet\CCMSCommon\Model\CompositeKey;

class DiskIOUsage extends Model
{
    use CompositeKey;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = "compute_instance_disk_io_usages";

    protected $primaryKey = [
        "compute_instance_id",
        "id",
    ];

    protected $guarded = [];
}
