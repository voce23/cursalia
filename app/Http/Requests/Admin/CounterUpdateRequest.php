<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CounterUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'counter_one_title' => ['nullable', 'string', 'max:120'],
            'counter_one_value' => ['required', 'integer', 'min:0'],
            'counter_two_title' => ['nullable', 'string', 'max:120'],
            'counter_two_value' => ['required', 'integer', 'min:0'],
            'counter_three_title' => ['nullable', 'string', 'max:120'],
            'counter_three_value' => ['required', 'integer', 'min:0'],
            'counter_four_title' => ['nullable', 'string', 'max:120'],
            'counter_four_value' => ['required', 'integer', 'min:0'],
        ];
    }
}
