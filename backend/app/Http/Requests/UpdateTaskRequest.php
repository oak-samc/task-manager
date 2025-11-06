<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|required|in:pending,in_progress,completed',
            'project_id' => 'sometimes|required|exists:projects,id',
            'task_list_id' => 'sometimes|nullable|exists:task_lists,id',
            'position' => 'sometimes|nullable|numeric'
        ];
    }
}
