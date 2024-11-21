<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{

    use AuthorizesRequests;

    /**
     * Get all applications for the authenticated company user.
     * Requires 'company' role and company_id check.
     */
    public function getAllApplications(Request $request)
    {
        $user = Auth::user();

        // Ensure the user is a company user
        if ($user->role !== 'company') {
            return response()->json(['message' => 'Only company users can access this endpoint'], 403);
        }

        $companyId = $user->company_id;

        // Get pagination parameters from the request, with defaults
        $size = $request->input('size', 10);
        $page = $request->input('page', 1);

        // Get sorting and filtering parameters
        $sortOrder = $request->input('sort_order', 'desc'); // 'asc' for Oldest, 'desc' for Newest
        $statusFilter = $request->input('status'); // Status filter (e.g., 'submitted', 'rejected', 'interview', 'offer')
        $searchKeyword = $request->input('search'); // Search keyword

        // Build the query for applications related to the company's vacancies
        $applicationsQuery = Application::whereHas('vacancy', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        });

        // Apply status filter if provided
        if ($statusFilter) {
            $applicationsQuery->where('status', $statusFilter);
        }

        // Apply search filter if provided
        if ($searchKeyword) {
            $applicationsQuery->where(function ($query) use ($searchKeyword) {
                $query->whereHas('vacancy', function ($q) use ($searchKeyword) {
                    $q->where('job_role', 'like', '%' . $searchKeyword . '%')
                        ->orWhere('id', 'like', '%' . $searchKeyword . '%');
                })
                    ->orWhere('first_name', 'like', '%' . $searchKeyword . '%')
                    ->orWhere('email', 'like', '%' . $searchKeyword . '%');
            });
        }

        // Apply sorting by created_at
        $applicationsQuery->orderBy('created_at', $sortOrder);

        // Paginate the results
        $applications = $applicationsQuery->paginate($size, ['*'], 'page', $page);

        return response()->json($applications);
    }

    /**
     * Get a specific application by its ID.
     * Ensures the application belongs to a vacancy of the authenticated company user.
     */
    public function getApplicationById($id)
    {
        $user = Auth::user();

        // Ensure the user is a company user
        if ($user->role !== 'company') {
            return response()->json(['message' => 'Only company users can access this endpoint'], 403);
        }

        $companyId = $user->company_id;

        // Find the application and ensure it belongs to the company's vacancy
        $application = Application::where('id', $id)
            ->whereHas('vacancy', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->first();

        if (!$application) {
            return response()->json(['message' => 'Application not found or access denied'], 404);
        }

        return response()->json($application);
    }

    // Get applications by vacancy ID
    public function getByVacancy($vacancyId)
    {
        $applications = Application::with('jobSeeker.user')
            ->where('vacancy_id', $vacancyId)
            ->get();

        return response()->json($applications);
    }

    public function apply(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'vacancy_id' => 'required|exists:vacancies,id',
            'first_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'job_title' => 'nullable|string',
            'linkedin' => 'nullable|url',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        // Handle file upload if a CV file is provided
        $cvLink = null;
        if ($request->hasFile('cv_file')) {
            $cvLink = $request->file('cv_file')->store('cv_files', 'public');
        }

        // Create a new application
        $application = Application::create([
            'vacancy_id' => $request->vacancy_id,
            'first_name' => $request->first_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'job_title' => $request->job_title,
            'linkedin' => $request->linkedin,
            'status' => 'submitted',
            'cv_link' => $cvLink,
        ]);

        return response()->json([
            'message' => 'Application submitted successfully',
            'application' => $application,
        ], 201);
    }

    public function getByCompanyId(Request $request, $companyId)
    {
        // Get pagination parameters from the request, with defaults
        $size = $request->input('size', 10); // Default size is 10 if not specified
        $page = $request->input('page', 1); // Default page is 1 if not specified

        // Fetch applications related to the company with pagination
        $applications = Application::whereHas('vacancy', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })
            ->orderBy('created_at', 'desc') // Order by newest first
            ->paginate($size, ['*'], 'page', $page);

        return response()->json($applications);
    }

    // Delete application by ID
    public function destroy($id)
    {
        $application = Application::findOrFail($id);

        $this->authorize('delete', $application);

        $application->delete();

        return response()->json(['message' => 'Application deleted successfully']);
    }

    // Search applications
    public function search(Request $request)
    {
        $query = Application::query();

        if ($request->has('applicant_name')) {
            $query->whereHas('jobSeeker.user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->applicant_name . '%');
            });
        }

        if ($request->has('vacancy_id')) {
            $query->where('vacancy_id', $request->vacancy_id);
        }

        if ($request->has('vacancy_name')) {
            $query->whereHas('vacancy', function ($q) use ($request) {
                $q->where('job_role', 'like', '%' . $request->vacancy_name . '%');
            });
        }

        $applications = $query->get();

        return response()->json($applications);
    }

    public function update(Request $request, $id)
    {
        $category = Application::findOrFail($id);

        $category->update($request->only('status'));

        return response()->json(['message' => 'Application updated successfully']);
    }
}
