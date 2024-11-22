<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'logo',
        'is_blocked',
        'description',
        //newly added
        'startup_stage',
        'startup_size',
        'open_to_remote',
        'funding',
        'industry_id'
    ];

    protected $hidden=[];

    protected $casts = [
        'logo' => 'string',
    ];

    public function vacancies()
    {
        return $this->hasMany(Vacancy::class);
    }

    public function industry()
    {
        return $this->hasOne(Industry::class);
    }

}
