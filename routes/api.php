<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\BlogController;
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

// Companies Public Routes
Route::get('companies', [CompanyController::class, 'index']);
Route::get('companies/{id}', [CompanyController::class, 'show']);
Route::get('companies/{companyId}/vacancies', [VacancyController::class, 'getByCompany']);

// Industry Public Routes
Route::get('industries', [IndustryController::class, 'index']);
Route::get('industries/{id}', [IndustryController::class, 'show']);
Route::get('industries/{id}/companies', [IndustryController::class, 'getTotalCompaniesCountById']);

// Categories Public Routes
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{id}', [CategoryController::class, 'show']);


// Applications Public Routes
Route::post('apply', [ApplicationController::class, 'apply']);

Route::get('vacancies/filter', [VacancyController::class, 'filter']);
Route::get('vacancies/{id}', [VacancyController::class, 'show']);
Route::get('vacancies', [VacancyController::class, 'index']);
Route::get('vacancies/category/{categoryIds}', [VacancyController::class, 'getByCategory']);

// Blogs Public Routes
Route::get('blogs', [BlogController::class, 'index']);
Route::get('blogs/{id}', [BlogController::class, 'show']);


// Protected Routes (Authenticated using Sanctum)
Route::middleware('auth:sanctum')->group(function () {
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


        Route::post('blogs', [BlogController::class, 'store']);
        Route::post('blogs/{id}', [BlogController::class, 'update']);
        Route::delete('blogs', [BlogController::class, 'destroy']);
    });

    // Company-only routes
    Route::middleware([CheckRole::class . ':company'])->group(function () {
        Route::put('vacancies/{id}', [VacancyController::class, 'update']);
        Route::post('vacancies', [VacancyController::class, 'store']);
        Route::delete('companies/{id}', [CompanyController::class, 'destroy']);
        Route::get('companies/vacancies/{vacancyId}', [CompanyController::class, 'getVacanciesByCompanyId']);
        Route::post('companies/{id}', [CompanyController::class, 'update']);
        Route::get('companies/{companyId}/applications', [ApplicationController::class, 'getByCompanyId']);
        Route::get('applications', [ApplicationController::class, 'getAllApplications']);
        Route::get('applications/{id}', [ApplicationController::class, 'getApplicationById']);
        Route::put('applications/{id}', [ApplicationController::class, 'update']);
    });

    // Vacancy APIs
    Route::post('vacancies/{id}/deactivate', [VacancyController::class, 'deactivate']);
    Route::post('vacancies', [VacancyController::class, 'store']);
    Route::post('vacancies/{id}/activate', [VacancyController::class, 'activate']);
    Route::post('vacancies/{id}/block', [VacancyController::class, 'block']);
    Route::post('vacancies/{id}/unblock', [VacancyController::class, 'unblock']);
    Route::delete('vacancies/{id}', [VacancyController::class, 'destroy']);
    Route::delete('vacancies', [VacancyController::class, 'destroy']);

    // Category APIs
    Route::middleware(['auth:sanctum', CheckRole::class . ':admin'])->group(function () {
        Route::post('categories', [CategoryController::class, 'store']);
        Route::put('categories/{id}', [CategoryController::class, 'update']);
        Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
    });

});