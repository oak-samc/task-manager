<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'project_id' => 'sometimes|required|exists:projects,id',
            'user_id' => 'sometimes|required|exists:users,id',
            'position' => 'sometimes|numeric',
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
            'user_id.required' => 'O usuário é obrigatório.',
            'user_id.exists' => 'O usuário selecionado não existe.',
            'position.numeric' => 'A posição deve ser um número.',
        ];
    }
}
