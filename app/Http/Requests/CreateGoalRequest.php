<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_\.!@#$%^&*()+=\/]+$/' // Allow common characters
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'target_value' => [
                'required',
                'integer',
                'min:1',
                'max:999999'
            ],
            'current_value' => [
                'required',
                'integer',
                'min:0'
            ],
            'deadline' => [
                'nullable',
                'date',
                'after:today'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The goal title is required.',
            'title.min' => 'The goal title must be at least 3 characters.',
            'title.regex' => 'The goal title contains invalid characters.',
            'target_value.required' => 'The target value is required.',
            'target_value.min' => 'The target value must be at least 1.',
            'current_value.required' => 'The current value is required.',
            'current_value.min' => 'The current value cannot be negative.',
            'deadline.after' => 'The deadline must be a future date.',
        ];
    }
}
