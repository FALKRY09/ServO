<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    protected $fillable = ['ticket_id', 'message_id', 'uploaded_by_id', 'original_name', 'stored_name', 'path', 'disk', 'mime_type', 'size_bytes'];

    public function ticket() { return $this->belongsTo(Ticket::class); }
}
