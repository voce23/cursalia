<?php

namespace App\Http\Requests\Frontend;

use App\Helpers\MathCaptcha;
use Illuminate\Foundation\Http\FormRequest;

class BlogCommentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'comment' => ['required', 'string', 'min:5', 'max:1000'],
            'captcha_token' => ['required', 'string'],
            'captcha_answer' => ['required', 'integer'],
        ];
    }

    /** Validación extra: comparar respuesta con captcha cifrado. */
    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            if (! MathCaptcha::verify($this->input('captcha_token'), $this->input('captcha_answer'))) {
                $v->errors()->add('captcha_answer', 'La respuesta no coincide. ¿Eres humano? 😊 Inténtalo de nuevo.');
            }
        });
    }
}
