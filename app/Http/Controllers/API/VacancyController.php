<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVacancyRequest;
use App\Http\Requests\UpdateVacancyRequest;
use App\Http\Resources\VacancyAdminListResource;
use App\Http\Resources\VacancyCompanyResource;
use App\Http\Resources\VacancyListResource;
use App\Http\Resources\VacancyResource;
use App\Models\Vacancy;
use App\Services\VacancyService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class VacancyController extends Controller
{

    use AuthorizesRequests;

    protected $vacancyService;

    public function __construct(VacancyService $vacancyService)
    {
        $this->vacancyService = $vacancyService;
    }

    public function index()
    {
        $vacancies = $this->vacancyService->listAllVacancies();
        return VacancyResource::collection($vacancies);
    }

    public function store(StoreVacancyRequest $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;

        if (!$companyId) {
            return response()->json(['message' => 'User with the company role must have an associated company'], 403);
        }

        $vacancy = $this->vacancyService->createVacancy($request->validated(), $companyId);
        return new VacancyResource($vacancy);
    }

    public function show($id)
    {
        $token = request()->bearerToken();

        $user = $token ? PersonalAccessToken::findToken($token)?->tokenable : null;

        if ($user && $user->role === 'company') {
            $vacancy = $this->vacancyService->companyGetById($id);
            return new VacancyCompanyResource($vacancy);
        } else {
            $vacancy = $this->vacancyService->getById($id);


            return new VacancyResource($vacancy);
        }
    }

    public function update(UpdateVacancyRequest $request, $id)
    {
        $vacancy = Vacancy::findOrFail($id);
        $this->authorize('update', $vacancy);

        $updatedVacancy = $this->vacancyService->updateVacancy($vacancy, $request->validated());
        return new VacancyResource($updatedVacancy);
    }

    public function destroy(Request $request)
    {
        // Get the IDs from the request
        $ids = $request->input('ids');

        // Ensure `ids` is provided and is an array
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['error' => 'Invalid request. Please provide an array of IDs.'], 400);
        }

        // Find vacancies by IDs
        $vacancies = Vacancy::whereIn('id', $ids)->get();

        // Check authorization for each vacancy
        foreach ($vacancies as $vacancy) {
            $this->authorize('delete', $vacancy);
        }

        // Delete the vacancies
        $response = $this->vacancyService->deleteVacancies($vacancies);

        return response()->json($response);
    }

    public function deactivate($id)
    {
        $vacancy = Vacancy::with('company')->findOrFail($id);
        $this->authorize('deactivate', $vacancy);

        $response = $this->vacancyService->deactivateVacancy($vacancy);
        return response()->json($response);
    }

    public function activate($id)
    {
        $vacancy = Vacancy::with('company')->findOrFail($id);
        $this->authorize('activate', $vacancy);

        $response = $this->vacancyService->activateVacancy($vacancy);
        return response()->json($response);
    }

    public function block($id)
    {
        $vacancy = Vacancy::findOrFail($id);
        $this->authorize('block', $vacancy);

        $response = $this->vacancyService->blockVacancy($vacancy);
        return response()->json($response);
    }

    public function unblock($id)
    {
        $vacancy = Vacancy::findOrFail($id);
        $this->authorize('block', $vacancy);

        $response = $this->vacancyService->unblockVacancy($vacancy);
        return response()->json($response);
    }

    public function getByCategory($categoryIds)
    {
        $ids = explode(',', $categoryIds);
        $vacancies = $this->vacancyService->getVacanciesByCategory($ids);
        return response()->json($vacancies);
    }

    public function filter(Request $request)
    {
        $filters = $request->all();
        $vacancies = $this->vacancyService->filterVacancies($filters);

        return response()->json(['data' => VacancyListResource::collection($vacancies), 'total' => $vacancies->count()]
        );
    }

    public function getByCompany($companyId)
    {
        $token = request()->bearerToken();

        $user = $token ? PersonalAccessToken::findToken($token)?->tokenable : null;

        $size = request()->query('size', 10);
        $page = request()->query('page', 1);

        // Get search filters
        $filters = [
            'title' => request()->query('title'),
            'jobType' => $this->splitFilter(request()->query('jobType')),
            'seniorityLevel' => $this->splitFilter(request()->query('seniorityLevel')),
            'vacancyId' => request()->query('vacancyId'),
        ];

        if ($user && $user->role === 'company') {
            $vacancies = $this->vacancyService->getByCompany($companyId, $size, $page, $filters);
            return VacancyCompanyResource::collection($vacancies);
        } else {
            $vacancies = $this->vacancyService->getActiveByCompany($companyId, $size, $page, $filters);
            return VacancyResource::collection($vacancies);
        }
    }

    private function splitFilter($filter)
    {
        return $filter ? explode('+', $filter) : [];
    }

}
