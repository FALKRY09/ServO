<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request) { return Category::orderBy('name')->when($request->search, fn ($q, $v) => $q->where('name', 'like', "%{$v}%"))->when($request->has('is_active'), fn ($q) => $q->where('is_active', $request->boolean('is_active')))->paginate(20); }

    public function show(Category $category) { return $category; }

    public function store(Request $request) {
        $data = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:categories,name'], 'description' => ['nullable', 'string']]);
        return response()->json(Category::create($data), 201);
    }

    public function update(Request $request, Category $category) {
        $data = $request->validate(['name' => ['sometimes', 'required', 'string', 'max:255', 'unique:categories,name,'.$category->id], 'description' => ['nullable', 'string'], 'is_active' => ['sometimes', 'boolean']]);
        $category->update($data);
        return $category->fresh();
    }

    public function toggleActive(Category $category) { $category->update(['is_active' => ! $category->is_active]); return $category->fresh(); }
}
