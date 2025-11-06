<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Normaliza/limpa entrada antes da validação
        if ($this->has('name')) {
            $this->merge([
                'name' => is_string($this->name) ? trim($this->name) : $this->name,
            ]);
        }
    }


    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da lista é obrigatório.',
            'name.max' => 'O nome não pode ter mais que 255 caracteres.',
            'user_id.exists' => 'o usuário especificado não existe.',
        ];
    }


}
