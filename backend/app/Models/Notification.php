<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'ticket_id', 'type', 'title', 'body', 'read_at', 'data'];
    protected $casts = ['read_at' => 'datetime'];
}
