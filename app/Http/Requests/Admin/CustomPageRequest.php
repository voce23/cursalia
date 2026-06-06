<?php

namespace App\Http\Requests\Admin;

use App\Models\CustomPage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customPage = $this->route('custom_page');

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique(CustomPage::class, 'slug')->ignore($customPage),
            ],
            'description' => ['required', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:255'],
            'show_at_nav' => ['nullable', 'boolean'],
            'status' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.regex' => 'La URL solo puede contener letras minúsculas, números y guiones.',
        ];
    }
}