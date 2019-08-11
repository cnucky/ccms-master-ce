<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use YunInternet\CCMSCommon\Model\CompositeKey;

class ComputeNodePublicISO extends Model
{
    use CompositeKey;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = "compute_node_public_isos";

    protected $primaryKey = ["compute_node_id", "internal_name"];

    protected $guarded = [];
}
