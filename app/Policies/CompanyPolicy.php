<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CompanyPolicy
{
    public function update(User $user, Company $company)
    {
        return $user->id === $company->user_id || $user->role === 'admin';
    }

    public function delete(User $user, Company $company)
    {
//        dd($company->attributesToArray(), $user->attributesToArray());
        return $user->id === $company->user_id || $user->role === 'admin';
    }

    public function block(User $user)
    {
        return $user->role === 'admin';
    }

    public function adminIndex(User $user)
    {
        return $user->role === 'admin';
    }

    public function unblock(User $user)
    {
        return $user->role === 'admin';
    }
}
