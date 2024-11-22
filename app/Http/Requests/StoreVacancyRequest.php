<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVacancyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only allow users with the 'company' role to make this request
        return auth()->user()->role === 'company';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'description' => 'required|string',
            'categoryId' => 'required|exists:categories,id',
            'isRemote' => 'boolean',
            'isActive' => 'boolean',
            'jobType' => 'required|in:full-time,part-time,contract,internship',
            'seniorityLevel' => 'required|string',
            'salary' => 'nullable|numeric',
            'jobOverview' => 'required|array',
            'city' => 'nullable|string',
            'country' => 'required|string',
            'countryCode' => 'required|string',
            'state' => 'required|string',
            'jobRole' => 'required|array',
            'jobResponsibilities' => 'required|array',
            'youHaveText' => 'required|array',
            'requirements' => 'nullable|array',
        ];
    }

    /**
     * Map camelCase input fields to snake_case for the database.
     * @param null $key
     * @param null $default
     */
    public function validated($key = null, $default = null): array
    {
        $data = parent::validated();

        return [
            'title' => $data['title'],
            'description' => $data['description'],
            'category_id' => $data['categoryId'],
            'is_remote' => $data['isRemote'] ?? false,
            'is_blocked' => false,
            'is_active' => $data['isActive'] ?? false,
            'job_type' => $data['jobType'],
            'city' => $data['city'],
            'country' => $data['country'],
            'country_code' => $data['countryCode'],
            'state' => $data['state'],
            'seniority_level' => $data['seniorityLevel'],
            'salary' => $data['salary'],
            'job_overview' => json_encode($data['jobOverview']),
            'job_role' => json_encode($data['jobRole']),
            'job_responsibilities' => json_encode($data['jobResponsibilities']),
            'you_have_text' => json_encode($data['youHaveText']),
            'requirements' => isset($data['requirements']) ? json_encode($data['requirements']) : null,
        ];
    }

}
