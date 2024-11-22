<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\VacancyAdminListResource;
use App\Http\Resources\VacancyAdminResource;
use App\Http\Resources\VacancyCompanyResource;
use App\Http\Resources\VacancyDashboardResource;
use App\Http\Resources\VacancyListResource;
use App\Http\Resources\VacancyResource;
use App\Models\Company;
use App\Models\Vacancy;
use App\Services\VacancyService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AdminController extends Controller
{
    use AuthorizesRequests;

    protected $vacancyService;

    public function __construct(VacancyService $vacancyService)
    {
        $this->vacancyService = $vacancyService;
    }

    // Block company by ID
    public function blockCompany($id)
    {
        $company = Company::findOrFail($id);
        $company->is_blocked = true;
        $company->save();
    }

    public function filterVacancies(Request $request)
    {
        $filters = $request->all();
        $vacancies = $this->vacancyService->filterVacanciesAdmin($filters);


        return response()
            ->json(['data' => VacancyAdminListResource::collection($vacancies),
                'total' => $vacancies->count()]);
    }


    public function showVacancy($id)
    {
        $vacancy = $this->vacancyService->adminGetById($id);
        return new VacancyAdminResource($vacancy);
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


}
