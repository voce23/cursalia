<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FeatureSectionUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'features' => ['required', 'array', 'min:1'],
            'features.*.id' => ['required', 'integer', 'exists:feature_sections,id'],
            'features.*.icon' => ['nullable', 'string', 'max:80'],
            'features.*.title' => ['required', 'string', 'max:120'],
            'features.*.description' => ['nullable', 'string', 'max:500'],
            'features.*.sort_order' => ['required', 'integer', 'min:1', 'max:999'],
            'features.*.is_active' => ['nullable', 'boolean'],
        ];
    }
}
