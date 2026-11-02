<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;

class TicketController extends Controller
{
    private const STATUSES = ['Open', 'Assigned', 'In Progress', 'Pending', 'Resolved', 'Closed'];

    private const TRANSITIONS = [
        'Open' => ['Assigned', 'In Progress'],
        'Assigned' => ['In Progress', 'Pending'],
        'In Progress' => ['Pending', 'Resolved'],
        'Pending' => ['In Progress'],
        'Resolved' => ['Closed', 'In Progress'],
        'Closed' => [],
    ];

    public function index(Request $request)
    {
        $user = $request->user();
        $query = Ticket::with(['requester', 'category', 'priority'])->latest();

        if (in_array($user->role?->name, ['Admin', 'Koordinator'], true)) {
            // Authorized staff can view all tickets in this phase.
        } elseif ($user->role?->name === 'Petugas') {
            $query->whereHas('assignments', fn ($assignment) => $assignment->where('assignee_id', $user->id)->whereNull('unassigned_at'));
        } else {
            $query->where('requester_id', $user->id);
        }

        $query->when($request->search, fn ($q, $value) => $q->where(fn ($q) => $q->where('ticket_number', 'like', "%{$value}%")->orWhere('title', 'like', "%{$value}%")))
            ->when($request->status, fn ($q, $value) => $q->where('status', $value))
            ->when($request->priority_id, fn ($q, $value) => $q->where('priority_id', $value))
            ->when($request->category_id, fn ($q, $value) => $q->where('category_id', $value));

        return $query->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'priority_id' => ['required', 'exists:priorities,id'],
        ]);

        $ticket = Ticket::create($data + [
            'ticket_number' => 'BT-'.now()->format('YmdHis').'-'.random_int(100, 999),
            'requester_id' => $request->user()->id,
            'status' => 'Open',
            'last_activity_at' => now(),
        ]);

        $ticket->histories()->create(['user_id' => $request->user()->id, 'event_type' => 'created', 'to_status' => 'Open']);
        User::whereHas('role', fn ($q) => $q->whereIn('name', ['Admin', 'Koordinator']))->get()->each(fn ($user) => Notification::create(['user_id' => $user->id, 'ticket_id' => $ticket->id, 'type' => 'ticket_created', 'title' => 'New ticket', 'body' => "Ticket {$ticket->ticket_number} was created."]));
        ActivityLog::create(['user_id' => $request->user()->id, 'action' => 'ticket_created', 'subject_type' => Ticket::class, 'subject_id' => $ticket->id]);

        return response()->json($ticket->load(['category', 'priority']), 201);
    }

    public function show(Request $request, Ticket $ticket)
    {
        $this->authorizeTicket($request, $ticket);
        return $ticket->load(['requester', 'department', 'category', 'priority', 'histories.user', 'assignments.assignee', 'assignments.assignedBy']);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $this->authorizeTicket($request, $ticket);
        $role = $request->user()->role?->name;
        $rules = ['title' => ['sometimes', 'required', 'string', 'max:255'], 'description' => ['sometimes', 'required', 'string']];
        if (in_array($role, ['Admin', 'Koordinator'], true)) {
            $rules['priority_id'] = ['sometimes', 'required', 'exists:priorities,id'];
            $rules['category_id'] = ['sometimes', 'required', 'exists:categories,id'];
            $rules['department_id'] = ['nullable', 'exists:departments,id'];
        }
        $data = $request->validate($rules);

        if ($role === 'Pelapor' && $ticket->status !== 'Open') {
            return response()->json(['message' => 'Only open tickets can be edited by the requester.'], 422);
        }
        if ($role === 'Petugas' && ! $ticket->assignments()->where('assignee_id', $request->user()->id)->whereNull('unassigned_at')->exists()) {
            abort(403, 'You are not authorized to edit this ticket.');
        }

        $ticket->update($data + ['last_activity_at' => now()]);
        $ticket->histories()->create(['user_id' => $request->user()->id, 'event_type' => 'updated', 'new_values' => json_encode(array_keys($data))]);
        return $ticket->fresh()->load(['category', 'priority', 'department']);
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $this->authorizeTicket($request, $ticket);
        $data = $request->validate(['status' => ['required', Rule::in(self::STATUSES)], 'note' => ['nullable', 'string']]);
        $from = $ticket->status;

        $isStaff = in_array($request->user()->role?->name, ['Admin', 'Koordinator', 'Petugas'], true);
        if (! $isStaff && ! ($request->user()->role?->name === 'Pelapor' && $from === 'Resolved' && $data['status'] === 'Closed')) {
            return response()->json(['message' => 'Your role cannot perform this status transition.'], 403);
        }

        if (! in_array($data['status'], self::TRANSITIONS[$from] ?? [], true)) {
            return response()->json(['message' => "Transition from {$from} to {$data['status']} is not allowed."], 422);
        }

        $ticket->update([
            'status' => $data['status'],
            'resolved_at' => $data['status'] === 'Resolved' ? now() : ($data['status'] === 'In Progress' ? null : $ticket->resolved_at),
            'closed_at' => $data['status'] === 'Closed' ? now() : ($data['status'] === 'In Progress' ? null : $ticket->closed_at),
            'last_activity_at' => now(),
        ]);
        $ticket->histories()->create(['user_id' => $request->user()->id, 'event_type' => 'status_changed', 'from_status' => $from, 'to_status' => $data['status'], 'note' => $data['note'] ?? null]);
        Notification::create(['user_id' => $ticket->requester_id, 'ticket_id' => $ticket->id, 'type' => 'status_changed', 'title' => 'Ticket status updated', 'body' => "Ticket {$ticket->ticket_number} is now {$data['status']}."]);
        ActivityLog::create(['user_id' => $request->user()->id, 'action' => 'ticket_status_changed', 'subject_type' => Ticket::class, 'subject_id' => $ticket->id, 'metadata' => json_encode(['from' => $from, 'to' => $data['status']])]);

        return $ticket->fresh()->load('histories.user');
    }

    private function authorizeTicket(Request $request, Ticket $ticket): void
    {
        $user = $request->user();
        if (in_array($user->role?->name, ['Admin', 'Koordinator'], true) || $ticket->requester_id === $user->id || $ticket->assignments()->where('assignee_id', $user->id)->whereNull('unassigned_at')->exists()) {
            return;
        }

        abort(403, 'You are not authorized to access this ticket.');
    }
}
