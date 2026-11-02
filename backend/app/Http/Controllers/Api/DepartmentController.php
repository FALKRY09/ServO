<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request) { return Department::orderBy('name')->when($request->search, fn ($q, $v) => $q->where('name', 'like', "%{$v}%"))->paginate(20); }

    public function show(Department $department) { return $department; }

    public function members(Department $department) { return $department->users()->select('id', 'name', 'email', 'role_id', 'is_active')->paginate(20); }

    public function store(Request $request) {
        $data = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:departments,name'], 'description' => ['nullable', 'string']]);
        return response()->json(Department::create($data), 201);
    }

    public function update(Request $request, Department $department) {
        $data = $request->validate(['name' => ['sometimes', 'required', 'string', 'max:255', 'unique:departments,name,'.$department->id], 'description' => ['nullable', 'string'], 'is_active' => ['sometimes', 'boolean']]);
        $department->update($data);
        return $department->fresh();
    }
}
