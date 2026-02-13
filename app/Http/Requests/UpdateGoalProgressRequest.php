<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGoalProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_value' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_value.required' => 'The current value is required.',
            'current_value.min' => 'The current value must be at least 0.',
        ];
    }
}
