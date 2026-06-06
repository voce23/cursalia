<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class ContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:120'],
            'email'          => ['required', 'email', 'max:255'],
            'subject'        => ['required', 'string', 'max:150'],
            'message'        => ['required', 'string', 'max:4000'],
            'captcha_token'  => ['required', 'string'],
            'captcha_answer' => ['required', 'integer'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            if (! \App\Helpers\MathCaptcha::verify($this->input('captcha_token'), $this->input('captcha_answer'))) {
                $v->errors()->add('captcha_answer', 'La respuesta no coincide. ¿Eres humano? 😊 Inténtalo de nuevo.');
            }
        });
    }
}
