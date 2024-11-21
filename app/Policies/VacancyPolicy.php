<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Auth\Access\Response;

class VacancyPolicy
{
    public function update(User $user, Vacancy $vacancy)
    {
        return $user->id === $vacancy->company->user_id || $user->role === 'admin';
    }

    public function delete(User $user, Vacancy $vacancy)
    {
        return ($vacancy->company && $user->id === $vacancy->company->user_id) || $user->role === 'admin';
    }

    public function deactivate(User $user, Vacancy $vacancy)
    {
        return ($vacancy->company && $user->id === $vacancy->company->user_id) || $user->role === 'admin';
    }

    public function activate(User $user, Vacancy $vacancy)
    {
        return ($vacancy->company && $user->id === $vacancy->company->user_id) || $user->role === 'admin';
    }


    public function block(User $user)
    {
        return $user->role === 'admin';
    }
}
