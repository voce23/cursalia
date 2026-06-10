<?php

namespace App\Http\Requests\Admin;

use App\Models\Blog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', Rule::unique(Blog::class, 'title')],
            'thumbnail' => ['required', 'image', 'mimes:jpeg,png,webp', 'max:3072'],
            'blog_category_id' => ['required', 'integer', Rule::exists('blog_categories', 'id')->where('status', true)],
            'summary' => ['required', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'status' => ['required', Rule::in(['draft', 'published'])],
        ];
    }
}
