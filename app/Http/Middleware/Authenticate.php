<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Return `null` for API requests to prevent redirection
        if ($request->expectsJson()) {
            return null;
        }

        // Otherwise, redirect to the login route (for web requests)
        return route('login');
    }
}
