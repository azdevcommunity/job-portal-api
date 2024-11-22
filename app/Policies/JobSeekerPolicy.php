<?php

namespace App\Policies;

use App\Models\JobSeeker;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JobSeekerPolicy
{
    public function update(User $user, JobSeeker $jobSeeker)
    {
        return $user->id === $jobSeeker->user_id;
    }

    public function delete(User $user, JobSeeker $jobSeeker)
    {
        return $user->id === $jobSeeker->user_id;
    }

    public function view(User $user, JobSeeker $jobSeeker)
    {
        return $user->id === $jobSeeker->user_id;
    }
}
