<?php

namespace App\ComputeInstance\Device;

use App\PublicFloppy;
use App\PublicFloppyCategory;
use Illuminate\Database\Eloquent\Model;

class Floppy extends Model
{
    protected $table = "compute_instance_floppies";

    protected $guarded = [];

    public function media()
    {
        return $this->belongsTo(PublicFloppy::class, "relative_media_id");
    }
}
