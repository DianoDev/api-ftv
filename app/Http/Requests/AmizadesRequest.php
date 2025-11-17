<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AmizadesRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'amigo_id' => 'required|exists:users,id',
        ];

        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'amigo_id.required' => 'O ID do usuário é obrigatório.',
            'amigo_id.exists' => 'O usuário selecionado não existe.',
        ];
    }
}
