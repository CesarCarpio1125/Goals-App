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
            'title' => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:1000',
            'deadline' => 'nullable|date|after:today',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The goal title is required.',
            'title.min' => 'The goal title must be at least 3 characters.',
            'deadline.after' => 'The deadline must be a future date.',
        ];
    }
}
