<?php

namespace App\Http\Requests\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255', Rule::unique(Admin::class)->ignore(auth('admin')->id())],
            'bio'             => ['nullable', 'string', 'max:6000'],
            'image'           => ['nullable', 'image', 'mimes:jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:2048'],
            // E-E-A-T para Schema.org Person.
            'headline'        => ['nullable', 'string', 'max:180'],
            'social_x'        => ['nullable', 'url', 'max:255'],
            'social_linkedin' => ['nullable', 'url', 'max:255'],
            'social_github'   => ['nullable', 'url', 'max:255'],
            'social_youtube'  => ['nullable', 'url', 'max:255'],
            'social_web'      => ['nullable', 'url', 'max:255'],
        ];
    }
}
