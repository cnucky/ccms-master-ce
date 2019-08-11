<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PublicISO extends Model
{
    protected $table = "public_isos";

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(PublicISOCategory::class, "category_id");
    }
}
