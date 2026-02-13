<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'target_value' => ['required', 'integer', 'min:1'],
            'current_value' => ['nullable', 'integer', 'min:0'],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The goal title is required.',
            'title.max' => 'The goal title may not be greater than 255 characters.',
            'description.max' => 'The description may not be greater than 2000 characters.',
            'target_value.required' => 'The target value is required.',
            'target_value.min' => 'The target value must be at least 1.',
            'current_value.min' => 'The current value must be at least 0.',
            'deadline.after_or_equal' => 'The deadline must be today or a future date.',
        ];
    }
}
