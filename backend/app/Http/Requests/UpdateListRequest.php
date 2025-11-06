<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $this->merge([
                'name' => is_string($this->name) ? trim($this->name) : $this->name,
            ]);

        }
    }




    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'user_id' => 'sometimes|nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da lista é obrigatório quando enviado.',
            'name.max' => 'O nome não pode ter mais que 255 caracteres.',
        ];
    }
}
