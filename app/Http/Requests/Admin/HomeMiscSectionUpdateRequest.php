<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HomeMiscSectionUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'newsletter_title' => ['nullable', 'string', 'max:255'],
            'newsletter_subtitle' => ['nullable', 'string', 'max:255'],
            'instructor_banner_title' => ['nullable', 'string', 'max:255'],
            'instructor_banner_subtitle' => ['nullable', 'string', 'max:255'],
            'instructor_banner_button_text' => ['nullable', 'string', 'max:80'],
            'instructor_banner_button_url' => ['nullable', 'string', 'max:255'],
            'video_section_title' => ['nullable', 'string', 'max:255'],
            'video_url' => ['nullable', 'string', 'max:255'],
        ];
    }
}
