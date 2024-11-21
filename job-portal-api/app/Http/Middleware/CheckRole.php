<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if the authenticated user's role is in the allowed roles
        if (!in_array($request->user()->role, $roles)) {
            return response()->json(['message' => 'Access Denied'], 403);
        }

        return $next($request);
    }
}
