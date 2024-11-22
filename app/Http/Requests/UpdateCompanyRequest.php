<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorize this request
    }

    public function rules(): array
    {
        return [
            'name' => 'string|nullable',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'string|nullable',
            'startup_stage' => 'string|nullable',
            'startup_size' => 'string|nullable',
            'open_to_remote' => 'string|nullable',
            'funding' => 'string|nullable',
            'industry_id' => 'numeric|nullable'
        ];
    }

    protected function prepareForValidation()
    {
        $snakeCased = $this->convertKeysToSnakeCase($this->all());
        $this->replace($snakeCased);
    }


    private function convertKeysToSnakeCase(array $data): array
    {
        return collect($data)
            ->mapWithKeys(fn($value, $key) => [Str::snake($key) => $value])
            ->toArray();
    }
}
