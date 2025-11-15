<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArenasRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string|max:1000',
            'cnpj' => 'nullable|string|max:20',
            'endereco' => 'required|string|max:500',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|size:2',
            'cep' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'telefone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'fotos' => 'nullable|json',
            'horario_funcionamento' => 'nullable|json',
            'comodidades' => 'nullable|json',
            'ativo' => 'sometimes|boolean',
        ];

        // Se estiver atualizando, os campos obrigatórios tornam-se opcionais
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['nome'] = 'sometimes|string|max:100';
            $rules['endereco'] = 'sometimes|string|max:500';
            $rules['cidade'] = 'sometimes|string|max:100';
            $rules['estado'] = 'sometimes|string|size:2';
        }

        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome da arena é obrigatório.',
            'nome.max' => 'O nome não pode ter mais de 100 caracteres.',
            'endereco.required' => 'O endereço é obrigatório.',
            'endereco.max' => 'O endereço não pode ter mais de 500 caracteres.',
            'cidade.required' => 'A cidade é obrigatória.',
            'estado.required' => 'O estado é obrigatório.',
            'estado.size' => 'O estado deve ter 2 caracteres (UF).',
            'latitude.between' => 'A latitude deve estar entre -90 e 90.',
            'longitude.between' => 'A longitude deve estar entre -180 e 180.',
            'fotos.json' => 'As fotos devem estar em formato JSON.',
            'horario_funcionamento.json' => 'O horário de funcionamento deve estar em formato JSON.',
            'comodidades.json' => 'As comodidades devem estar em formato JSON.',
        ];
    }
}
