<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseCategoryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'max:255', Rule::unique('course_categories')],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:3072'],
        ];
    }
}
