<?php

namespace App\Ticket;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = "ticket_departments";

    protected $guarded = [];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, "department_id");
    }
}
