<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ContactSettingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'form_title' => ['nullable', 'string', 'max:255'],
            'form_subtitle' => ['nullable', 'string', 'max:255'],
            'receiver_email' => ['nullable', 'email', 'max:255'],
            'map_embed_url' => ['nullable', 'string', 'max:2000'],
            'schedule' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
