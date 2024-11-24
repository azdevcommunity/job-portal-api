<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\VacancyAdminResource;
use App\Models\Company;
use App\Models\Vacancy;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{

    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = Company::withCount('vacancies');

        $query->where(function ($companyQuery) use ($request) {
            $companyQuery->where(function ($subQuery) use ($request) {
                if ($request->filled('name')) {
                    $subQuery->where('name', 'LIKE', '%' . $request->input('name') . '%');
                }

                if ($request->filled('vacancyName')) {
                    $subQuery->orWhereHas('vacancies', function ($vacancyQuery) use ($request) {
                        $vacancyQuery->where('title', 'LIKE', '%' . $request->input('vacancyName') . '%');
                    });
                }
            });

            $companyQuery->where(function ($subQuery) use ($request) {
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

        if ($request->filled('sortBy')) {
            $sortOrder = strtolower($request->input('sortBy')) === 'asc' ? 'asc' : 'desc';
            $query->orderBy('created_at', $sortOrder);
        }

        if ($request->filled('industryId')) {
            $query->orWhere('industry_id', $request->input('industryId'));
        }

        if ($request->filled('startupStage')) {
            $query->orWhere('startup_stage', $request->input('startupStage'));
        }

        if ($request->filled('startupSize')) {
            $query->orWhere('startup_size', $request->input('startupSize'));
        }

        if ($request->filled('openToRemote')) {
            $query->orWhere('open_to_remote', $request->input('openToRemote'));
        }

        if ($request->filled('funding')) {
            $query->orWhere('funding', $request->input('funding'));
        }

        $query->where('is_blocked', false);

        $companies = $query->get();

        return CompanyResource::collection($companies);
    }

    public function getVacanciesByCompanyId($vacancyId)
    {
        $vacancy = Vacancy::where('id', $vacancyId)->first();
        $company = Company::where('id', $vacancy->company_id)->first();

        $vacancy->company_name = $company->name;
        return new VacancyAdminResource($vacancy);
    }

    public function show($id)
    {
        $company = Company::where('id', $id)
            ->where('is_blocked', false)
            ->firstOrFail();
        return new CompanyResource($company);
    }

    public function update(UpdateCompanyRequest $request, $id)
    {
        $company = Company::findOrFail($id);

        $this->authorize('update', $company);

        if ($request->hasFile('logo')) {
            if ($company->logo && Storage::exists(str_replace('/storage/', '', $company->logo))) {
                Storage::delete(str_replace('/storage/', '', $company->logo));
            }

            $logoPath = $request->file('logo')->store('logos', 'public');
            $company->logo = '/storage/' . $logoPath;
        }

        $company->update(array_merge(
            $request->validated(),
            ['logo' => $company->logo]
        ));

        return response()->json([
            'message' => 'Company updated successfully',
            'company' => $company,
        ]);
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);

        $this->authorize('delete', $company);

        $company->delete();

        return response()->json(['message' => 'Company deleted successfully']);
    }

}