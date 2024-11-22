<?php

namespace App\Services;

use App\Http\Resources\BlogsResource;
use App\Models\Company;
use App\Models\Blog;
use Illuminate\Support\Facades\DB;

class BlogsService
{
    public function listAllBlogs()
    {
        return Blog::where('Blogs.is_active', true) // Explicitly qualify "is_active"
        ->join('categories', 'Blogs.category_id', '=', 'categories.id') // Join with companies table
        ->select('Blogs.*', 'categories.name as categories_name') // Select Blogs fields and company name
        ->get();
    }

    public function createBlogs(array $data)
    {
        $BlogsData = array_merge($data);

        $Blogs = Blog::create($BlogsData);

        return $Blogs;
    }

    public function updateBlogs(Blog $Blogs, array $data)
    {
        $filteredData = array_filter($data, function ($value) {
            return $value !== null;
        });

        $Blogs->update($filteredData);
        return $Blogs;
    }

    public function deleteBlogs($Blog)
    {
        foreach ($Blog as $Blog) {
            $Blog->delete();
        }

        return ['message' => 'Blogs deleted successfully'];
    }

    public function deactivateBlogs(Blog $Blogs)
    {
        $Blogs->update(['is_active' => false]);
        return ['message' => 'Blogs deactivated successfully'];
    }

    public function getById($id)
    {
        return Blog::query()
            ->join('categories', 'Blogs.category_id', '=', 'categories.id')
            ->select('Blogs.*', 'categories.name as categories_name')
            ->where('Blogs.is_active', true)
            ->findOrFail($id);
    }


    public function adminGetById($id)
    {
        return Blog::with('company', 'category')
            ->findOrFail($id);
    }

    public function activateBlogs(Blog $Blogs)
    {
        $Blogs->update(['is_active' => true]);
        return ['message' => 'Blogs activated successfully'];
    }

    public function blockBlogs(Blog $Blogs)
    {
        $Blogs->update(['is_blocked' => true, 'is_active' => false]);
        return ['message' => 'Blogs blocked successfully'];
    }

    public function unblockBlogs(Blog $Blogs)
    {
        $Blogs->update(['is_blocked' => false]);
        return ['message' => 'Blogs unblocked successfully'];
    }

    public function getBlogsByCategory(array $categoryIds)
    {
        return Blog::whereIn('category_id', $categoryIds)->get();
    }

    public function filterBlogs(array $filters)
    {
        $query = Blog::query();
        $query->join('companies', 'Blogs.company_id', '=', 'companies.id')
            ->select('Blogs.*', 'companies.name as company_name', 'companies.logo as logo');
        $query->where('Blogs.is_active', true)
            ->where('Blogs.is_blocked', false);
        $filterMappings = [
            'jobType' => 'job_type',
            'seniorityLevel' => 'seniority_level',
            'categoryId' => 'category_id',
            'isRemote' => 'is_remote',
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

        if (!empty($filters['title'])) {
            $title = strtolower($filters['title']);
            $query->whereRaw('LOWER(Blogs.title) LIKE ?', ["%$title%"]);
        }

        $sortBy = $filters['sortBy'] ?? 'desc';
        $query->orderBy('created_at', $sortBy);

        return $query->get();
    }

    public function filterBlogsAdmin(array $filters)
    {
        $query = Blog::query();
        $query->join('companies', 'Blogs.company_id', '=', 'companies.id')
            ->select('Blogs.*', 'companies.name as company_name', 'companies.logo as logo');
        $filterMappings = [
            'jobType' => 'job_type',
            'seniorityLevel' => 'seniority_level',
            'categoryId' => 'category_id',
            'isRemote' => 'is_remote',
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

        if (!empty($filters['title'])) {
            $title = strtolower($filters['title']);
            $query->whereRaw('LOWER(Blogs.title) LIKE ?', ["%$title%"]);
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

        // Filter by BlogsId
        if (!empty($filters['BlogsId'])) {
            $query->where('id', $filters['BlogsId']);
        }

        return $query;
    }

    public function getByCompany($companyId, $size, $page, $filters)
    {
        $company = Company::findOrFail($companyId);

        if ($company->is_blocked) {
            return [];
        }

        $query = Blog::where('company_id', $companyId);

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

        $query = Blog::where('company_id', $companyId)
            ->where('is_active', true)
            ->where('is_blocked', false);

        // Apply filters
        $query = $this->applyFilters($query, $filters);

        return $query->paginate($size, ['*'], 'page', $page);
    }

    public function listBlogsByCompanyId($companyId)
    {
        return Blog::where('company_id', $companyId)
            ->where('is_active', true)
            ->where('is_blocked', false)
            ->get();
    }
}
