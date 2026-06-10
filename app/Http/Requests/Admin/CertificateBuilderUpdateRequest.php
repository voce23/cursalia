<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CertificateBuilderUpdateRequest extends FormRequest
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
            'description' => ['nullable', 'string', 'max:1000'],
            'background' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:4096'],
            'signature' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:4096'],
        ];
    }
}
