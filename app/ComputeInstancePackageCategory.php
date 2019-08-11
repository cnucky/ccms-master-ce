<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComputeInstancePackageCategory extends Model
{
    protected $guarded = [];

    public function packages()
    {
        return $this->hasMany(ComputeInstancePackage::class, "category_id");
    }

    public function instances()
    {
        return $this->hasManyThrough(ComputeInstance::class, ComputeInstancePackage::class, "category_id", "compute_instance_package_id", "id", "id");
    }
}
