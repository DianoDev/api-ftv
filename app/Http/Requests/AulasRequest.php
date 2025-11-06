<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AulasRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // Adicione suas regras de validação aqui
            // 'nome' => ['required', 'string', 'max:128'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [];
    }
}
