<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'task_list_id' => 'nullable|exists:task_lists,id',
            'position' => 'nullable|numeric',
            'status' => 'nullable|in:pending,in_progress,completed'
        ];
    }
}
