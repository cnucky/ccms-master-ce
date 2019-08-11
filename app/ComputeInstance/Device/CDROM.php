<?php

namespace App\ComputeInstance\Device;

use App\PublicISO;
use Illuminate\Database\Eloquent\Model;

class CDROM extends Model
{
    protected $table = "compute_instance_device_cd_roms";

    protected $guarded = [];

    public function media()
    {
        return $this->belongsTo(PublicISO::class, "relative_media_id");
    }
}
