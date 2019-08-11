<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PublicFloppyCategory extends Model
{
    protected $table = "public_floppy_categories";

    protected $guarded = [];

    public function publicFloppies()
    {
        return $this->hasMany(PublicFloppy::class, "category_id");
    }
}
