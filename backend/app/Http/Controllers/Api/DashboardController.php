<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function summary(Request $request)
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $query = $this->scopedTickets($request);
        if (! empty($validated['from'])) $query->whereDate('created_at', '>=', $validated['from']);
        if (! empty($validated['to'])) $query->whereDate('created_at', '<=', $validated['to']);

        $summary = [
            'total' => (clone $query)->count(),
            'by_status' => (clone $query)->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status'),
            'by_priority' => (clone $query)->join('priorities', 'priorities.id', '=', 'tickets.priority_id')->select('priorities.name', DB::raw('count(*) as total'))->groupBy('priorities.id', 'priorities.name')->pluck('total', 'name'),
            'by_category' => (clone $query)->join('categories', 'categories.id', '=', 'tickets.category_id')->select('categories.name', DB::raw('count(*) as total'))->groupBy('categories.id', 'categories.name')->pluck('total', 'name'),
            'resolved' => (clone $query)->whereNotNull('resolved_at')->count(),
            'closed' => (clone $query)->whereNotNull('closed_at')->count(),
        ];
        $role = $request->user()->role?->name;
        if ($role === 'Admin') {
            $summary['users'] = User::where('is_active', true)->count();
            $summary['active_staff'] = User::where('is_active', true)->whereHas('role', fn ($q) => $q->where('name', 'Petugas'))->count();
            $summary['recent_activity'] = \App\Models\ActivityLog::latest()->limit(10)->get();
        } elseif ($role === 'Koordinator') {
            $summary['unassigned'] = (clone $query)->whereDoesntHave('assignments', fn ($q) => $q->whereNull('unassigned_at'))->count();
            $summary['in_progress'] = (clone $query)->where('status', 'In Progress')->count();
            $summary['pending'] = (clone $query)->where('status', 'Pending')->count();
            $summary['sla_overdue'] = null;
        } elseif ($role === 'Petugas') {
            $summary['assigned'] = $summary['total'];
            $summary['pending'] = (clone $query)->where('status', 'Pending')->count();
        } else {
            $summary['own_in_progress'] = (clone $query)->whereIn('status', ['Assigned', 'In Progress', 'Pending'])->count();
            $summary['own_completed'] = (clone $query)->whereIn('status', ['Resolved', 'Closed'])->count();
        }
        return response()->json($summary);
    }

    public function report(Request $request)
    {
        $data = $request->validate([
            'from' => ['nullable', 'date'], 'to' => ['nullable', 'date', 'after_or_equal:from'],
            'status' => ['nullable', 'string'], 'priority_id' => ['nullable', 'exists:priorities,id'],
            'category_id' => ['nullable', 'exists:categories,id'], 'department_id' => ['nullable', 'exists:departments,id'],
            'assignee_id' => ['nullable', 'exists:users,id'], 'group_by' => ['nullable', 'in:status,priority,category,department,assignee'],
        ]);
        $query = $this->scopedTickets($request)->with(['priority:id,name', 'category:id,name', 'department:id,name']);
        foreach (['status', 'priority_id', 'category_id', 'department_id'] as $filter) if (isset($data[$filter])) $query->where($filter, $data[$filter]);
        if (isset($data['assignee_id'])) $query->whereHas('assignments', fn ($q) => $q->where('assignee_id', $data['assignee_id'])->whereNull('unassigned_at'));
        if (isset($data['from'])) $query->whereDate('created_at', '>=', $data['from']);
        if (isset($data['to'])) $query->whereDate('created_at', '<=', $data['to']);
        if (isset($data['group_by'])) {
            $column = ['priority' => 'priority_id', 'category' => 'category_id', 'department' => 'department_id', 'status' => 'status'][$data['group_by']] ?? null;
            if ($column) return response()->json(['group_by' => $data['group_by'], 'data' => $query->select($column, DB::raw('count(*) as total'))->groupBy($column)->get()]);
        }
        return response()->json(['data' => $query->latest()->paginate(20)]);
    }

    private function scopedTickets(Request $request)
    {
        $user = $request->user();
        $query = Ticket::query();
        if ($user->role?->name === 'Petugas') $query->whereHas('assignments', fn ($q) => $q->where('assignee_id', $user->id)->whereNull('unassigned_at'));
        elseif ($user->role?->name === 'Pelapor') $query->where('requester_id', $user->id);
        return $query;
    }
}
