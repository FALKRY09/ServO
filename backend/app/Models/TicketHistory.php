<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketHistory extends Model
{
    protected $fillable = ['ticket_id', 'user_id', 'event_type', 'from_status', 'to_status', 'old_values', 'new_values', 'note'];

    public function ticket() { return $this->belongsTo(Ticket::class); }
    public function user() { return $this->belongsTo(User::class); }
}
