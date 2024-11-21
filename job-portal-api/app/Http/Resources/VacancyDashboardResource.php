<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VacancyDashboardResource extends JsonResource
{

    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'companyId' => $this->company_id,
            'description' => $this->description,
            'categoryId' => $this->category_id,
            'isRemote' => $this->is_remote,
            'jobType' => $this->job_type,
            'seniorityLevel' => $this->seniority_level,
            'salary' => $this->salary,
            'city' => $this->city,
            'country' => $this->country,
            'countryCode' => $this->country_code,
            'state' => $this->state,
            'jobOverview' => json_decode($this->job_overview),
            'jobRole' => json_decode($this->job_role),
            'jobResponsibilities' => json_decode($this->job_responsibilities),
            'youHaveText' => json_decode($this->you_have_text),
            'isBlocked' => $this->is_blocked,
            'isActive' => $this->is_active,
            'title' => $this->title,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }

}
