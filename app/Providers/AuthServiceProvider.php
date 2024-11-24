<?php

namespace App\Providers;

use App\Models\Blog;
use App\Models\Company;
use App\Models\Vacancy;
use App\Policies\BlogPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\VacancyPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Company::class => CompanyPolicy::class,
        Vacancy::class => VacancyPolicy::class,
        Blog::class => BlogPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Define any additional Gates or authorization logic here if needed
    }
}
