<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $data = $request->validate(['assignee_id' => ['required', 'exists:users,id'], 'note' => ['nullable', 'string']]);
        $assignee = User::with('role')->findOrFail($data['assignee_id']);
        abort_unless($assignee->is_active && $assignee->role?->name === 'Petugas', 422, 'The assignee must be an active Petugas.');

        $ticket->assignments()->whereNull('unassigned_at')->update(['unassigned_at' => now()]);
        $assignment = $ticket->assignments()->create($data + ['assigned_by_id' => $request->user()->id, 'assigned_at' => now()]);
        $ticket->update(['status' => 'Assigned', 'last_activity_at' => now()]);
        $ticket->histories()->create(['user_id' => $request->user()->id, 'event_type' => 'assigned', 'to_status' => 'Assigned', 'note' => $data['note'] ?? null]);
        Notification::create(['user_id' => $assignee->id, 'ticket_id' => $ticket->id, 'type' => 'ticket_assigned', 'title' => 'Ticket assigned', 'body' => "Ticket {$ticket->ticket_number} was assigned to you."]); 
        ActivityLog::create(['user_id' => $request->user()->id, 'action' => 'ticket_assigned', 'subject_type' => Ticket::class, 'subject_id' => $ticket->id, 'metadata' => json_encode(['assignee_id' => $assignee->id])]);

        return response()->json($assignment->load('assignee'), 201);
    }

    public function accept(Request $request, TicketAssignment $assignment)
    {
        abort_unless($assignment->assignee_id === $request->user()->id && ! $assignment->unassigned_at, 403);
        $assignment->update(['accepted_at' => now()]);
        ActivityLog::create(['user_id' => $request->user()->id, 'action' => 'assignment_accepted', 'subject_type' => TicketAssignment::class, 'subject_id' => $assignment->id]);
        return $assignment->fresh();
    }
}
