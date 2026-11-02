<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Notification;

class MessageController extends Controller
{
    public function index(Request $request, Ticket $ticket)
    {
        $this->authorizeTicket($request, $ticket);
        $query = $ticket->messages()->with('user:id,name');
        if ($request->user()->role?->name === 'Pelapor') $query->where('is_internal', false);
        return $query->latest()->paginate(20);
    }

    public function store(Request $request, Ticket $ticket)
    {
        $this->authorizeTicket($request, $ticket);
        $data = $request->validate(['body' => ['required', 'string'], 'is_internal' => ['sometimes', 'boolean']]);
        if ($data['is_internal'] ?? false) abort_unless(in_array($request->user()->role?->name, ['Admin', 'Koordinator'], true), 403);
        $message = $ticket->messages()->create($data + ['user_id' => $request->user()->id]);
        if ($ticket->requester_id !== $request->user()->id && ! ($data['is_internal'] ?? false)) {
            Notification::create(['user_id' => $ticket->requester_id, 'ticket_id' => $ticket->id, 'type' => 'new_message', 'title' => 'New ticket message', 'body' => "A new message was added to {$ticket->ticket_number}."]);
        }
        ActivityLog::create(['user_id' => $request->user()->id, 'action' => 'ticket_message_created', 'subject_type' => Ticket::class, 'subject_id' => $ticket->id]);
        return response()->json($message, 201);
    }

    private function authorizeTicket(Request $request, Ticket $ticket): void
    {
        $user = $request->user();
        $assigned = $ticket->assignments()->where('assignee_id', $user->id)->whereNull('unassigned_at')->exists();
        abort_unless(in_array($user->role?->name, ['Admin', 'Koordinator'], true) || $ticket->requester_id === $user->id || $assigned, 403);
    }
}
