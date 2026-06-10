<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InstructorPaymentGatewayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::in(['paypal', 'bank_transfer', 'stripe_connect', 'other'])],
            'instructions' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la pasarela es obligatorio.',
            'type.required' => 'El tipo de pasarela es obligatorio.',
            'type.in' => 'El tipo seleccionado no es válido.',
        ];
    }
}
