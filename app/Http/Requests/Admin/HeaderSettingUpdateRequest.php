<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HeaderSettingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_button_text' => ['required', 'string', 'max:80'],
            'category_limit' => ['required', 'integer', 'min:1', 'max:20'],
            'show_search' => ['nullable', 'boolean'],
            'search_placeholder' => ['required', 'string', 'max:120'],
        ];
    }
}