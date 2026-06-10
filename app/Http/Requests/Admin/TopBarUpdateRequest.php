<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TopBarUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['nullable', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:60'],
            'offer_text' => ['nullable', 'string', 'max:255'],
            'offer_url' => ['nullable', 'string', 'max:255'],
            'background_color' => ['required', 'string', 'max:20'],
            'text_color' => ['required', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
