<?php

namespace App\ComputeNode;

use Illuminate\Database\Eloquent\Model;
use YunInternet\CCMSCommon\Model\CompositeKey;

class CPUUsage extends Model
{
    use CompositeKey;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = "compute_node_cpu_usages";

    protected $primaryKey = [
        "compute_node_id",
        "id",
        "processor",
    ];

    protected $guarded = [];

    public function getUtilizationAttribute()
    {
        return $this->user + $this->nice + $this->system + $this->irq + $this->softirq + $this->steal;
    }
}
