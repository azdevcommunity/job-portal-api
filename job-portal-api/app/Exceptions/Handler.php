<?php

namespace App\Exceptions;
use Illuminate\Auth\Access\AuthorizationException;
use NunoMaduro\Collision\Adapters\Laravel\ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthorizationException) {
            // Return 401 Unauthorized instead of 403 Forbidden
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return parent::render($request, $exception);
    }
}
