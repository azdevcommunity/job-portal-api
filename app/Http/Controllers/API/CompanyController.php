<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{

    use AuthorizesRequests;

    // List all companies
    public function index(Request $request)
    {
        $query = Company::withCount('vacancies');

        $query->where(function ($companyQuery) use ($request) {
            // Group conditions for name and vacancy-related filters
            $companyQuery->where(function ($subQuery) use ($request) {
                // Filter by company name
                if ($request->filled('name')) {
                    $subQuery->where('name', 'LIKE', '%' . $request->input('name') . '%');
                }

                // Filter by vacancy title (vacancyName)
                if ($request->filled('vacancyName')) {
                    $subQuery->orWhereHas('vacancies', function ($vacancyQuery) use ($request) {
                        $vacancyQuery->where('title', 'LIKE', '%' . $request->input('vacancyName') . '%');
                    });
                }
            });

            // Add filters for city and country if provided
            $companyQuery->where(function ($subQuery) use ($request) {
                // Filter by either vacancyCity or vacancyCountry
                if ($request->filled('vacancyCity') || $request->filled('vacancyCountry')) {
                    $subQuery->orWhereHas('vacancies', function ($vacancyQuery) use ($request) {
                        if ($request->filled('vacancyCity')) {
                            $vacancyQuery->where('city', 'LIKE', '%' . $request->input('vacancyCity') . '%');
                        }
                        if ($request->filled('vacancyCountry')) {
                            $vacancyQuery->orWhere('country', 'LIKE', '%' . $request->input('vacancyCountry') . '%');
                        }
                    });
                }
            });
        });

        // Sort companies by created_at
        if ($request->filled('sortBy')) {
            $sortOrder = strtolower($request->input('sortBy')) === 'asc' ? 'asc' : 'desc';
            $query->orderBy('created_at', $sortOrder);
        }

        // Filter companies that are not blocked
        $query->where('is_blocked', false);

        // Get the filtered companies
        $companies = $query->get();

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
    public function update(UpdateCompanyRequest $request, $id)
    {
        $company = Company::findOrFail($id);

        // Authorization check
        $this->authorize('update', $company);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete existing logo if it exists
            if ($company->logo && Storage::exists($company->logo)) {
                Storage::delete($company->logo);
            }

            // Store the new logo
            $logoPath = $request->file('logo')->store('logos', 'public');
            $company->logo = '/storage/' . $logoPath;
        }



        // Update other fields
        $company->update($request->validated());



        return response()->json([
            'message' => 'Company updated successfully',
            'company' => $company,
        ]);
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
