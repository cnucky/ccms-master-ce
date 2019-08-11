<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PublicISOCategory extends Model
{
    protected $table = "public_iso_categories";

    protected $guarded = [];

    public function publicIsos()
    {
        return $this->hasMany(PublicISO::class, "category_id");
    }
}
