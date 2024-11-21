<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'description',
        'category_id',
        'is_remote',
        'job_type',
        'seniority_level',
        'salary',
        'city',
        'country',
        'country_code',
        'state',
        'job_overview',
        'job_role',
        'job_responsibilities',
        'you_have_text',
        'is_blocked',
        'is_active',
        'title',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
