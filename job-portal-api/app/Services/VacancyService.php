<?php

namespace App\Services;

use App\Http\Resources\VacancyResource;
use App\Models\Company;
use App\Models\Vacancy;
use Illuminate\Support\Facades\DB;

class VacancyService
{
    public function listAllVacancies()
    {
        $vacancies = Vacancy::where('vacancies.is_active', true) // Explicitly qualify "is_active"
        ->where('vacancies.is_blocked', false) // Explicitly qualify "is_blocked"
        ->join('companies', 'vacancies.company_id', '=', 'companies.id') // Join with companies table
        ->select('vacancies.*', 'companies.name as company_name') // Select vacancies fields and company name
        ->get();

        return $vacancies;
    }

    public function createVacancy(array $data, int $companyId)
    {
        $vacancyData = array_merge($data, ['company_id' => $companyId]);

        $vacancy = Vacancy::create($vacancyData);

        $cc = Company::where('id', $companyId)->first();

        $vacancy->company_name = $cc->name;

        return $vacancy;
    }

    public function updateVacancy(Vacancy $vacancy, array $data)
    {
        $filteredData = array_filter($data, function ($value) {
            return $value !== null;
        });

        $vacancy->update($filteredData);
        return $vacancy;
    }

    public function deleteVacancies($vacancies)
    {
        foreach ($vacancies as $vacancy) {
            $vacancy->delete();
        }

        return ['message' => 'Vacancies deleted successfully'];
    }

    public function deactivateVacancy(Vacancy $vacancy)
    {
        $vacancy->update(['is_active' => false]);
        return ['message' => 'Vacancy deactivated successfully'];
    }

    public function getById($id)
    {
        return Vacancy::query()
            ->join('companies', 'vacancies.company_id', '=', 'companies.id')
            ->select('vacancies.*', 'companies.name as company_name')
            ->where('vacancies.is_active', true)
            ->where('vacancies.is_blocked', false)
            ->findOrFail($id);
    }

    public function companyGetById($id)
    {
        return Vacancy::with('company', 'category')
            ->findOrFail($id);
    }

    public function activateVacancy(Vacancy $vacancy)
    {
        $vacancy->update(['is_active' => true]);
        return ['message' => 'Vacancy activated successfully'];
    }

    public function blockVacancy(Vacancy $vacancy)
    {
        $vacancy->update(['is_blocked' => true, 'is_active' => false]);
        return ['message' => 'Vacancy blocked successfully'];
    }

    public function unblockVacancy(Vacancy $vacancy)
    {
        $vacancy->update(['is_blocked' => false]);
        return ['message' => 'Vacancy unblocked successfully'];
    }

    public function getVacanciesByCategory(array $categoryIds)
    {
        return Vacancy::whereIn('category_id', $categoryIds)->get();
    }

    public function filterVacancies(array $filters)
    {
        $query = Vacancy::query();
        $query->join('companies', 'vacancies.company_id', '=', 'companies.id')
            ->select('vacancies.*', 'companies.name as company_name', 'companies.logo as logo');
        $query->where('vacancies.is_active', true)
            ->where('vacancies.is_blocked', false);
        $filterMappings = [
            'jobType' => 'job_type',
            'seniorityLevel' => 'seniority_level',
            'categoryId' => 'category_id',
            'salaryMin' => ['salary', '>='],
            'salaryMax' => ['salary', '<='],
        ];

        foreach ($filterMappings as $requestField => $dbField) {
            if (isset($filters[$requestField])) {
                if (is_array($dbField)) {
                    $query->where($dbField[0], $dbField[1], $filters[$requestField]);
                } else {
                    $query->where($dbField, $filters[$requestField]);
                }
            }
        }

        $this->applyCaseInsensitiveFilter($query, $filters, 'city');
        $this->applyCaseInsensitiveFilter($query, $filters, 'country');

        // Apply the combined location filter
        if (isset($filters['location'])) {
            $location = strtolower($filters['location']);
            $query->whereRaw("LOWER(CONCAT(city, ', ', country)) LIKE ?", ["%$location%"]);
        }

        $sortBy = $filters['sortBy'] ?? 'desc';
        $query->orderBy('created_at', $sortBy);

        return $query->get();
    }

    protected function applyCaseInsensitiveFilter($query, array $filters, string $field): void
    {
        if (isset($filters[$field])) {
            $value = strtolower($filters[$field]);
            $query->where(DB::raw("LOWER($field)"), 'LIKE', "%{$value}%");
        }
    }

    public function applyFilters($query, $filters)
    {
        // Filter by title
        if (!empty($filters['title'])) {
            $query->where('title', 'LIKE', '%' . $filters['title'] . '%');
        }

        // Filter by jobType (array of job types)
        if (!empty($filters['jobType'])) {
            $query->whereIn('job_type', $filters['jobType']);
        }

        // Filter by seniorityLevel (array of seniority levels)
        if (!empty($filters['seniorityLevel'])) {
            $query->whereIn('seniority_level', $filters['seniorityLevel']);
        }

        // Filter by vacancyId
        if (!empty($filters['vacancyId'])) {
            $query->where('id', $filters['vacancyId']);
        }

        return $query;
    }

    public function getByCompany($companyId, $size, $page, $filters)
    {
        $company = Company::findOrFail($companyId);

        if ($company->is_blocked) {
            return [];
        }

        $query = Vacancy::where('company_id', $companyId);

        // Apply filters
        $query = $this->applyFilters($query, $filters);

        return $query->paginate($size, ['*'], 'page', $page);
    }

    public function getActiveByCompany($companyId, $size, $page, $filters)
    {
        $company = Company::findOrFail($companyId);

        if ($company->is_blocked) {
            return [];
        }

        $query = Vacancy::where('company_id', $companyId)
            ->where('is_active', true)
            ->where('is_blocked', false);

        // Apply filters
        $query = $this->applyFilters($query, $filters);

        return $query->paginate($size, ['*'], 'page', $page);
    }

    public function listVacanciesByCompanyId($companyId)
    {
        return Vacancy::where('company_id', $companyId)
            ->where('is_active', true)
            ->where('is_blocked', false)
            ->get();
    }
}
