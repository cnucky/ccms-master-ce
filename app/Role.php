<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends \Spatie\Permission\Models\Role
{
    public function admins()
    {
        return $this
            ->belongsToMany(Admin::class, "model_has_roles", "model_id", "role_id")
            ->wherePivot("model_type", Admin::class)
            ;
    }
}
