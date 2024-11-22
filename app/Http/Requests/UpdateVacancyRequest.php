<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateVacancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'company' || auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'title' => 'string|nullable',
            'description' => 'string|nullable',
            'category_id' => 'numeric|exists:categories,id|nullable',
            'is_remote' => 'boolean|nullable',
            'job_type' => 'in:full-time,part-time,contract,internship|nullable',
            'seniority_level' => 'in:intern,junior,middle,senior,teamlead|nullable',
            'salary' => 'numeric|nullable',
            'job_overview' => 'array|nullable',
            'city' => 'string|nullable',
            'country' => 'string|nullable',
            'country_code' => 'string|nullable',
            'state' => 'string|nullable',
            'job_role' => 'array|nullable',
            'job_responsibilities' => 'array|nullable',
            'you_have_text' => 'array|nullable',
            'is_blocked' => 'boolean|nullable',
            'is_active' => 'boolean|nullable',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation Error',
            'errors' => $validator->errors(),
        ], 400));
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'category_id' => $this->input('categoryId'),
            'is_remote' => $this->input('isRemote'),
            'job_type' => $this->input('jobType'),
            'city' => $this->input('city'),
            'description' => $this->input('description'),
            'country' => $this->input('country'),
            'country_code' => $this->input('countryCode'),
            'state' => $this->input('state'),
            'seniority_level' => $this->input('seniorityLevel'),
            'job_overview' => $this->input('jobOverview'),
            'job_role' => $this->input('jobRole'),
            'job_responsibilities' => $this->input('jobResponsibilities'),
            'you_have_text' => $this->input('youHaveText'),
            'is_blocked' => $this->input('isBlocked'),
            'is_active' => $this->input('isActive'),
        ]);
    }

    public function validated($key = null, $default = null): array
    {
        $data = parent::validated();

        return [
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'is_remote' => $data['is_remote'] ?? false,
            'job_type' => $data['job_type'] ?? null,
            'seniority_level' => $data['seniority_level'] ?? null,
            'salary' => $data['salary'] ?? null,
            'job_overview' => isset($data['job_overview']) ? json_encode($data['job_overview']) : null,
            'city' => $data['city'] ?? null,
            'country' => $data['country'] ?? null,
            'country_code' => $data['country_code'] ?? null,
            'state' => $data['state'] ?? null,
            'job_role' => isset($data['job_role']) ? json_encode($data['job_role']) : null,
            'job_responsibilities' => isset($data['job_responsibilities']) ? json_encode($data['job_responsibilities']) : null,
            'you_have_text' => isset($data['you_have_text']) ? json_encode($data['you_have_text']) : null,
            'is_blocked' => $data['is_blocked'] ?? false,
            'is_active' => $data['is_active'] ?? false,
        ];
    }
}
