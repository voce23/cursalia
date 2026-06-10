<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FooterUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => ['nullable', 'string', 'max:1200'],
            'contact_title' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:60'],
            'address' => ['nullable', 'string', 'max:255'],
            'bottom_text' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'dark' => ['nullable', 'boolean'],
        ];
    }
}
