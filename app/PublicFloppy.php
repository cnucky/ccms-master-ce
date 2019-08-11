<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PublicFloppy extends Model
{
    protected $table = "public_floppies";

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(PublicFloppyCategory::class, "category_id");
    }
}
