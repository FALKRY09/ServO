<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAssignment extends Model
{
    protected $fillable = ['ticket_id', 'assignee_id', 'assigned_by_id', 'assigned_at', 'accepted_at', 'unassigned_at', 'note'];
    protected $casts = ['assigned_at' => 'datetime', 'accepted_at' => 'datetime', 'unassigned_at' => 'datetime'];
    public function assignee() { return $this->belongsTo(User::class, 'assignee_id'); }
    public function assignedBy() { return $this->belongsTo(User::class, 'assigned_by_id'); }
}
