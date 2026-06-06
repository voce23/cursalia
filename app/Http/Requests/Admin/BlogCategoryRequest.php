<?php

namespace App\Http\Requests\Admin;

use App\Models\BlogCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $blogCategory = $this->route('blog_category');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique(BlogCategory::class, 'name')->ignore($blogCategory)],
            'color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'status' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'color.regex' => 'El color debe estar en formato hexadecimal, por ejemplo #4f46e5.',
        ];
    }
}