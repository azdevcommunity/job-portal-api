<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VacancyAdminListResource extends JsonResource
{

    public static $wrap = null;


    public function toArray($request)
    {


        return [
            'id' => $this->id,
            'companyId' => $this->company_id,
            'companyName' => $this->company_name,
            'description' => $this->description,
            'categoryId' => $this->category_id,
            'isRemote' => $this->is_remote,
            'salary' => $this->salary,
            'title' => $this->title,
            'jobType' => $this->job_type,
            'seniorityLevel' => $this->seniority_level,
            'city' => $this->city,
            'country' => $this->country,
            'countryCode' => $this->country_code,
            'isActive' => $this->is_active,
            'isBlocked' => $this->is_blocked,
            'state' => $this->state,
            'jobOverview' => json_decode($this->job_overview),
            'jobRole' => json_decode($this->job_role),
            'jobResponsibilities' => json_decode($this->job_responsibilities),
            'youHaveText' => json_decode($this->you_have_text),
            'createdAt' => $this->created_at,
            'logo' => $this->logo,
        ];
    }

}
