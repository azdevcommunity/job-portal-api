<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'vacancy_id',
        'first_name',
        'email',
        'phone',
        'job_title',
        'status',
        'linkedin',
        'cv_link'
    ];

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }
//
//    public function jobSeeker()
//    {
//        return $this->belongsTo(JobSeeker::class);
//    }
}
