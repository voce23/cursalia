<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'              => ['required', 'string', 'max:255'],
            'seo_description'    => ['nullable', 'string', 'max:255'],
            'category_id'        => ['required', Rule::exists('course_categories', 'id')->whereNotNull('parent_id')],
            'course_level_id'    => ['required', 'exists:course_levels,id'],
            'course_language_id' => ['required', 'exists:course_languages,id'],
            'thumbnail'          => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:3072'],
            'demo_video_storage' => ['nullable', 'string', 'in:youtube,vimeo,external_link'],
            'demo_video_source'  => ['nullable', 'string', 'max:1000'],
            'description'        => ['required', 'string'],
            'price'              => ['required', 'numeric', 'min:0'],
            'discount'           => ['nullable', 'numeric', 'min:0'],
            'duration'           => ['required', 'string', 'max:100'],
            'certificate'        => ['nullable', 'boolean'],
            'qna'                => ['nullable', 'boolean'],
        ];
    }
}
