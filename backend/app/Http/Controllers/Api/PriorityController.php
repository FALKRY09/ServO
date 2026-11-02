<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Priority;
use Illuminate\Http\Request;

class PriorityController extends Controller
{
    public function index(Request $request) { return Priority::orderBy('level')->when($request->search, fn ($q, $v) => $q->where('name', 'like', "%{$v}%"))->when($request->has('is_active'), fn ($q) => $q->where('is_active', $request->boolean('is_active')))->paginate(20); }

    public function show(Priority $priority) { return $priority; }

    public function store(Request $request) {
        $data = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:priorities,name'], 'level' => ['required', 'integer', 'min:1'], 'response_target_minutes' => ['nullable', 'integer', 'min:0'], 'resolution_target_minutes' => ['nullable', 'integer', 'min:0']]);
        return response()->json(Priority::create($data), 201);
    }

    public function update(Request $request, Priority $priority) {
        $data = $request->validate(['name' => ['sometimes', 'required', 'string', 'max:255', 'unique:priorities,name,'.$priority->id], 'level' => ['sometimes', 'integer', 'min:1'], 'response_target_minutes' => ['nullable', 'integer', 'min:0'], 'resolution_target_minutes' => ['nullable', 'integer', 'min:0'], 'is_active' => ['sometimes', 'boolean']]);
        $priority->update($data);
        return $priority->fresh();
    }

    public function toggleActive(Priority $priority) { $priority->update(['is_active' => ! $priority->is_active]); return $priority->fresh(); }
}
