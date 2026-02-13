<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The goal title is required.',
            'title.max' => 'The goal title may not be greater than 255 characters.',
            'description.max' => 'The description may not be greater than 2000 characters.',
            'deadline.after_or_equal' => 'The deadline must be today or a future date.',
        ];
    }
}
