<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = ['ticket_number', 'requester_id', 'department_id', 'category_id', 'priority_id', 'sla_policy_id', 'title', 'description', 'status', 'resolved_at', 'closed_at', 'last_activity_at'];

    protected $casts = ['resolved_at' => 'datetime', 'closed_at' => 'datetime', 'last_activity_at' => 'datetime'];

    public function requester() { return $this->belongsTo(User::class, 'requester_id'); }
    public function department() { return $this->belongsTo(Department::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function priority() { return $this->belongsTo(Priority::class); }
    public function histories() { return $this->hasMany(TicketHistory::class); }
    public function assignments() { return $this->hasMany(TicketAssignment::class); }
    public function messages() { return $this->hasMany(TicketMessage::class); }
    public function attachments() { return $this->hasMany(TicketAttachment::class); }
}
