<?php

namespace App\Policies;

use App\Models\Blog;
use App\Models\User;

class BlogPolicy
{
    public function update(User $user)
    {
        return $user->role === 'admin';
    }
}
