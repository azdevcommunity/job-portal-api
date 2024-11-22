<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\IndustryController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\VacancyController;
//use App\Http\Controllers\API\JobSeekerController;
use App\Http\Controllers\API\ApplicationController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\RegisterController;

// Auth Public Routes
Route::post('login', [AuthController::class, 'login']);
Route::post('refresh', [AuthController::class, 'refresh']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
Route::post('register', [RegisterController::class, 'register']);
Route::post('register/company', [RegisterController::class, 'registerCompany']);
//Route::post('register/user', [RegisterController::class, 'registerJobSeeker']);

// Companies Public Routes
Route::get('companies', [CompanyController::class, 'index']);
Route::get('companies/{id}', [CompanyController::class, 'show']);
Route::get('companies/{companyId}/vacancies', [VacancyController::class, 'getByCompany']);

// Industry Public Routes

Route::get('industries', [IndustryController::class, 'index']);
Route::get('industries/{id}', [IndustryController::class, 'show']);


// Categories Public Routes
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{id}', [CategoryController::class, 'show']);


// Applications Public Routes
Route::post('apply', [ApplicationController::class, 'apply']);

Route::get('vacancies/filter', [VacancyController::class, 'filter']);
Route::get('vacancies/{id}', [VacancyController::class, 'show']);
Route::get('vacancies', [VacancyController::class, 'index']);
Route::get('vacancies/category/{categoryIds}', [VacancyController::class, 'getByCategory']);


// Protected Routes (Authenticated using Sanctum)
Route::middleware('auth:sanctum')->group(function () {


// Vacancies Public Routes
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin-only routes
    Route::middleware([CheckRole::class . ':admin'])->group(function () {
        Route::post('admin/companies/{id}/block', [AdminController::class, 'blockCompany']);
        Route::post('admin/companies/{id}/unblock', [AdminController::class, 'unblockCompany']);
        Route::get('admin/companies/{companyId}', [AdminController::class, 'getByCompanyId']);
        Route::get('admin/companies', [AdminController::class, 'getAllCompanies']);
        Route::post('admin/companies', [AdminController::class, 'getAllCompanies']);
        Route::delete('admin/companies/{id}', [AdminController::class, 'destroyCompany']);
        // Vacancies API
        Route::get('admin/vacancies/{id}', [AdminController::class, 'showVacancy']);
        Route::get('admin/vacancies', [AdminController::class, 'filterVacancies']);
        // Industries API
        Route::post('admin/industries', [IndustryController::class, 'store']); // Create
        Route::put('admin/industries/{id}', [IndustryController::class, 'update']); // Update
        Route::delete('admin/industries/{id}', [IndustryController::class, 'destroy']); // Delete
        // Job seekers API
//        Route::get('admin/job_seekers/{id}', [AdminController::class, 'getJobSeekerById']);
//        Route::get('admin/job_seekers', [AdminController::class, 'getAllJobSeekers']);

        // TODO: implement /job_seekers POST method 2 ci qeydiyyat for apply detail
    });

    // Company-only routes
    Route::middleware([CheckRole::class . ':company'])->group(function () {
        Route::post('vacancies', [VacancyController::class, 'store']);
        Route::delete('companies/{id}', [CompanyController::class, 'destroy']);
        Route::post('companies/{id}', [CompanyController::class, 'update']);
        Route::get('companies/{companyId}/applications', [ApplicationController::class, 'getByCompanyId']);
        Route::get('applications', [ApplicationController::class, 'getAllApplications']);
        Route::get('applications/{id}', [ApplicationController::class, 'getApplicationById']);
        Route::put('applications/{id}', [ApplicationController::class, 'update']);

    });

    // Job Seeker-only routes
//    Route::middleware([CheckRole::class . ':job_seeker'])->group(function () {
//        Route::get('users/{id}', [JobSeekerController::class, 'show']);
//        Route::get('users', [JobSeekerController::class, 'index']);
//        Route::post('users', [JobSeekerController::class, 'store']);
//    });
    // Job Seeker APIs
//    Route::apiResource('job_seekers', JobSeekerController::class);
//    Route::get('job_seekers/{id}/jobs_applied', [JobSeekerController::class, 'jobsAppliedFor']);



    // Vacancy APIs
    Route::post('vacancies/{id}/deactivate', [VacancyController::class, 'deactivate']);
    Route::put('vacancies/{id}', [VacancyController::class, 'update']);
    Route::post('vacancies', [VacancyController::class, 'store']);
    Route::post('vacancies/{id}/activate', [VacancyController::class, 'activate']);
    Route::post('vacancies/{id}/block', [VacancyController::class, 'block']);
    Route::delete('vacancies/{id}', [VacancyController::class, 'destroy']);
    Route::delete('vacancies', [VacancyController::class, 'destroy']);
    Route::post('vacancies/{id}/block', [VacancyController::class, 'block']);
    Route::post('vacancies/{id}/unblock', [VacancyController::class, 'unblock']);

//    // Application APIs
//    Route::get('applications/vacancy/{vacancyId}', [ApplicationController::class, 'getByVacancy']);
//    Route::delete('applications/{id}', [ApplicationController::class, 'destroy']);
//    Route::get('applications/search', [ApplicationController::class, 'search']);

    // Category APIs
    Route::middleware(['auth:sanctum', CheckRole::class . ':admin'])->group(function () {
        Route::post('categories', [CategoryController::class, 'store']);
        Route::put('categories/{id}', [CategoryController::class, 'update']);
        Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
    });

});
