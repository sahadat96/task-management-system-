<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tasks' => 'required|array|min:1',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.description' => 'nullable|string',
            'tasks.*.status' => 'required|in:pending,in_progress,completed',
            'tasks.*.priority' => 'required|integer|min:1|max:5',
            'tasks.*.due_date' => 'nullable|date|after_or_equal:today',
        ];
    }

    public function messages()
    {
        return [
            'tasks.required' => 'At least one task is required',
            'tasks.*.title.required' => 'Each task must have a title',
            'tasks.*.status.in' => 'Status must be pending, in_progress, or completed',
            'tasks.*.priority.min' => 'Priority must be at least 1',
            'tasks.*.priority.max' => 'Priority cannot be more than 5',
            'tasks.*.due_date.after_or_equal' => 'Due date cannot be in the past',
        ];
    }
}
