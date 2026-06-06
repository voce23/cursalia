<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class LessonStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'storage'      => ['required', 'in:upload,youtube,vimeo,external_link'],
            'file_type'    => ['required', 'in:video,audio,doc,pdf,file'],
            'file_path'    => ['nullable', 'string'],
            'duration'     => ['nullable', 'string', 'max:20'],
            'description'  => ['nullable', 'string'],
            'is_preview'   => ['nullable', 'boolean'],
            'downloadable' => ['nullable', 'boolean'],
        ];
    }
}
