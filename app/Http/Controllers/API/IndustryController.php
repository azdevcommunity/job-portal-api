<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\IndustryResource;
use App\Models\Industry;
use Illuminate\Http\Request;

class IndustryController extends Controller
{
    // Public API to get all industries
    public function index()
    {
        return IndustryResource::collection(Industry::all());
    }

    // Public API to get all industries
    public function show($id)
    {
        return new IndustryResource(Industry::findOrFail($id));
    }

    // Create a new industry (Admin only)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $industry = Industry::create([
            'name' => $request->name,
        ]);

        return response()->json(['message' => 'Industry created successfully', 'industry' => $industry], 201);
    }

    // Update an industry (Admin only)
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $industry = Industry::findOrFail($id);
        $industry->update(['name' => $request->name]);

        return response()->json(['message' => 'Industry updated successfully', 'industry' => $industry], 200);
    }

    // Delete an industry (Admin only)
    public function destroy($id)
    {
        $industry = Industry::findOrFail($id);
        $industry->delete();

        return response()->json(['message' => 'Industry deleted successfully'], 200);
    }
}
