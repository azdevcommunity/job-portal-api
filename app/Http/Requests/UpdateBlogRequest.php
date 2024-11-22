<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'id' => 'required|exists:blogs,id',
            'title' => 'required|string',
            'content' => 'required|string',
            'category_id' => 'required|string',
            'is_active' => 'boolean',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $data = parent::validated();

        if ($this->hasFile('image')) {
            $path = $this->file('image')->store('images', 'public');
            $data['image'] = $path;
        }

        return [
            'id' => $data['id'],
            'title' => $data['title'],
            'content' => $data['content'],
            'category_id' => $data['category_id'],
            'is_active' => $data['is_active'] ?? false,
            'image' => $data['image'] ?? null,
        ];
    }
}
