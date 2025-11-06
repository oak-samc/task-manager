<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'nullable|exists:users,id',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'O nome da lista é obrigatório.',
            'name.string' => 'O nome da lista deve ser um texto.',
            'name.max' => 'O nome da lista não pode ter mais de 255 caracteres.',
            'project_id.required' => 'O projeto é obrigatório.',
            'project_id.exists' => 'O projeto selecionado não existe.',
            'user_id.exists' => 'O usuário selecionado não existe.',
        ];
    }
}
