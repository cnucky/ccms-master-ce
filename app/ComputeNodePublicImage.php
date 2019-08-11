<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use YunInternet\CCMSCommon\Model\CompositeKey;

class ComputeNodePublicImage extends Model
{
    use CompositeKey;

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = ["compute_node_id", "internal_name", "version"];

    protected $fillable = [
        "compute_node_id",
        "internal_name",
        "version",
    ];
}
