<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ApplicationPolicy
{
    public function delete(User $user, Application $application)
    {
        return $user->role === 'admin';
    }
}
