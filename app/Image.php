<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $guarded = [
        "id",
        "created_at",
        "updated_at",
    ];

    public function imageCategory()
    {
        return $this->belongsTo(ImageCategory::class);
    }
}
