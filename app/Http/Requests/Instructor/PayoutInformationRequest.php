<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class PayoutInformationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gateway_id'     => ['nullable', 'exists:instructor_payment_gateways,id'],
            'account_name'   => ['nullable', 'string', 'max:150'],
            'account_email'  => ['nullable', 'email', 'max:150'],
            'bank_name'      => ['nullable', 'string', 'max:150'],
            'account_number' => ['nullable', 'string', 'max:100'],
            'routing_number' => ['nullable', 'string', 'max:100'],
            'other_details'  => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'gateway_id'     => 'pasarela de pago',
            'account_name'   => 'nombre del titular',
            'account_email'  => 'email de cuenta',
            'bank_name'      => 'nombre del banco',
            'account_number' => 'número de cuenta',
            'routing_number' => 'código de enrutamiento',
            'other_details'  => 'detalles adicionales',
        ];
    }
}
