<?php

namespace App\Ticket;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $table = "ticket_replies";

    protected $guarded = [];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, "ticket_id");
    }
}
