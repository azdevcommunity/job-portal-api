<?php

namespace App\Http;

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckTokenExpiration;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class Kernel extends HttpKernel
{
    protected $routeMiddleware = [
        'role' => CheckRole::class,
        'auth' => Authenticate::class,
        'auth:sanctum' => EnsureFrontendRequestsAreStateful::class,
        'check.expiration' => CheckTokenExpiration::class,
    ];

}
