<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SlaPolicy;
use Illuminate\Http\Request;

class SlaPolicyController extends Controller
{
    public function index(Request $request) { return SlaPolicy::with(['priority', 'category'])->when($request->search, fn ($q, $v) => $q->where('name', 'like', "%{$v}%"))->paginate(20); }

    public function show(SlaPolicy $slaPolicy) { return $slaPolicy->load(['priority', 'category']); }

    public function store(Request $request) {
        $data = $this->validated($request);
        return response()->json(SlaPolicy::create($data)->load(['priority', 'category']), 201);
    }

    public function update(Request $request, SlaPolicy $slaPolicy) {
        $slaPolicy->update($this->validated($request, $slaPolicy->id));
        return $slaPolicy->fresh()->load(['priority', 'category']);
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:sla_policies,name'.($id ? ','.$id : '')],
            'description' => ['nullable', 'string'], 'priority_id' => ['nullable', 'exists:priorities,id'],
            'category_id' => ['nullable', 'exists:categories,id'], 'response_target_minutes' => ['nullable', 'integer', 'min:0'],
            'resolution_target_minutes' => ['nullable', 'integer', 'min:0'], 'business_hours_only' => ['sometimes', 'boolean'], 'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
