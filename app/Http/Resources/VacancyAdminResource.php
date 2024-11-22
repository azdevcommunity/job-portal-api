<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VacancyAdminResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'companyId' => $this->company_id,
            'categoryId' => $this->category_id,
            'description' => $this->description,
            'isRemote' => $this->is_remote,
            'salary' => $this->salary,
            'title' => $this->title,
            'jobType' => $this->job_type,
            'seniorityLevel' => $this->seniority_level,
            'city' => $this->city,
            'country' => $this->country,
            'countryCode' => $this->country_code,
            'state' => $this->state,
            'jobOverview' => json_decode($this->job_overview),
            'jobRole' => json_decode($this->job_role),
            'isActive' => $this->is_active,
            'isBlocked' => $this->is_blocked,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'jobResponsibilities' => json_decode($this->job_responsibilities),
            'youHaveText' => json_decode($this->you_have_text)
        ];
    }
}
