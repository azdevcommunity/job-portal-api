<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{

    use AuthorizesRequests;

    // List all companies
    public function index()
    {
        $companies = Company::where('is_blocked', false)->get();
        return CompanyResource::collection($companies);
    }

//    // Create a new company
//    public function store(Request $request)
//    {
//        $request->validate([
////            'name' => 'required|string',
//            'userId' => 'required|numeric',
//            'description' => 'nullable|string',
//        ]);
//
//        $user = User::findOrFail($request->userId);
//
//        if (!$user){
//            return response()->json(['message' => 'User not found.'], 404);
//        }
//
//        $company = Company::create([
//            'user_id' => auth()->id(),
//            'name' => $user->name,
//            'isBlocked' => $request->isBlocked,
//            'description' => $request->description,
//        ]);
//
//        return response()->json($company, 201);
//    }

    // Get company by ID
    public function show($id)
    {
        $company = Company::where('id', $id)
            ->where('is_blocked', false)
            ->firstOrFail();
        return new CompanyResource($company);
    }

// Update company by ID
    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $this->authorize('update', $company);

        $request->validate([
            'name' => 'string|nullable',
            'description' => 'string|nullable',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo && Storage::exists($company->logo)) {
                Storage::delete($company->logo);
            }
            $logoPath = $request->file('logo')->store('logos', 'public');
            $company->logo = '/storage/' . $logoPath;
        }

        $company->update($request->only(['name', 'description']));
        $company->save();

        return response()->json(['message' => 'Company updated successfully', 'company' => $company]);
    }

    // Delete company by ID
    public function destroy($id)
    {
        $company = Company::findOrFail($id);

        $this->authorize('delete', $company);

        $company->delete();

        return response()->json(['message' => 'Company deleted successfully']);
    }

}
