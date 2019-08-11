<?php

namespace App\Ticket;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class, "department_id");
    }

    public function replies()
    {
        return $this->hasMany(Reply::class, "ticket_id");
    }
}
