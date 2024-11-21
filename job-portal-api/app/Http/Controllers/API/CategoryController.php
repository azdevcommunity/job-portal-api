<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // List all categories
    public function index()
    {
        return Category::all();
    }

    // Create a new category
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $category = Category::create($request->only('name'));

        return response()->json($category, 201);
    }

    // Get category by ID
    public function show($id)
    {
        return Category::findOrFail($id);
    }

    // Update category by ID
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $category->update($request->only('name'));

        return response()->json(['message' => 'Category updated successfully']);
    }

    // Delete category by ID
    public function destroy($id)
    {
        Category::destroy($id);

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
