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
    ];

    public function vacancies()
    {
        return $this->hasMany(Vacancy::class);
    }

}