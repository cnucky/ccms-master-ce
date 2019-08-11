<?php

namespace App;

use App\Node\ComputeNode;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = [
        "name",
        "icon_class",
        "description",
        "status",
    ];

    public function zones()
    {
        return $this->hasMany(Zone::class);
    }

    public function computeNodes()
    {
        return $this->hasManyThrough(ComputeNode::class, Zone::class);
    }
}
