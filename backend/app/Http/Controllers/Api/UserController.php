<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function assignees() { return User::with('role')->where('is_active', true)->whereHas('role', fn ($q) => $q->where('name', 'Petugas'))->select('id', 'name', 'email', 'role_id')->orderBy('name')->get(); }

    public function index(Request $request) { return User::with(['role', 'department'])->select('id', 'role_id', 'department_id', 'name', 'email', 'is_active', 'last_login_at', 'created_at')->when($request->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('name', 'like', "%{$v}%")->orWhere('email', 'like', "%{$v}%")))->when($request->role_id, fn ($q, $v) => $q->where('role_id', $v))->when($request->department_id, fn ($q, $v) => $q->where('department_id', $v))->paginate(20); }

    public function show(User $user) { return $user->load('role', 'department'); }

    public function store(Request $request) {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255', 'unique:users,email'], 'password' => ['required', 'string', 'min:8'], 'role_id' => ['required', 'exists:roles,id'], 'department_id' => ['nullable', 'exists:departments,id'], 'is_active' => ['sometimes', 'boolean']]);
        $data['password'] = Hash::make($data['password']);
        return response()->json(User::create($data)->load('role', 'department'), 201);
    }

    public function update(Request $request, User $user) {
        $data = $request->validate(['name' => ['sometimes', 'required', 'string', 'max:255'], 'email' => ['sometimes', 'required', 'email', 'max:255', 'unique:users,email,'.$user->id], 'password' => ['sometimes', 'string', 'min:8'], 'role_id' => ['sometimes', 'required', 'exists:roles,id'], 'department_id' => ['nullable', 'exists:departments,id'], 'is_active' => ['sometimes', 'boolean']]);
        if (isset($data['password'])) { $data['password'] = Hash::make($data['password']); }
        $user->update($data);
        return $user->fresh()->load('role', 'department');
    }

    public function toggleActive(User $user) { $user->update(['is_active' => ! $user->is_active]); return $user->fresh()->load('role', 'department'); }
}
