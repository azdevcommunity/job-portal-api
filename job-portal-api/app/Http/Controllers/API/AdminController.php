<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\VacancyDashboardResource;
use App\Models\Company;
use App\Models\Vacancy;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdminController extends Controller
{
    use AuthorizesRequests;

    // Block company by ID
    public function blockCompany($id)
    {
        $company = Company::findOrFail($id);
        $company->is_blocked = true;
        $company->save();
    }

    // Unblock company by ID
    public function unblockCompany($id)
    {
        $company = Company::findOrFail($id);
        $company->is_blocked = false;
        $company->save();
    }

    public function getByCompanyId($companyId)
    {
        return Company::findOrFail($companyId);
    }

    public function getAllCompanies()
    {
        return Company::all();
    }


    // Vacancies API
    public function getVacancyById($id)
    {
        $vacancy = Vacancy::findOrFail($id);

        return new VacancyDashboardResource($vacancy);
    }

    public function getAllVacancies()
    {
        $vacancies = Vacancy::latest()->get();

        return VacancyDashboardResource::collection($vacancies);
    }

}
