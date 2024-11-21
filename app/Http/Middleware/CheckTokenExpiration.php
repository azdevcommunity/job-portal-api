<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTokenExpiration
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->currentAccessToken()->expires_at < now()) {
            return response()->json(['message' => 'Access token expired'], 401);
        }

        return $next($request);
    }
}
