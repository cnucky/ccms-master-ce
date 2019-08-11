<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImageCategory extends Model
{
    protected $guarded = [
        "id",
        "created_at",
        "updated_at",
    ];

    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
