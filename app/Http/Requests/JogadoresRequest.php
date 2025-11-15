<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JogadoresRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nivel' => 'sometimes|string|in:iniciante,intermediario,avancado,profissional',
            'lado_preferido' => 'sometimes|string|in:esquerda,direita,ambos',
            'posicao_preferida' => 'sometimes|string|max:50',
            'nivel_jogo' => 'sometimes|string|in:iniciante,intermediario,avancado,profissional',
            'ranking' => 'sometimes|integer|min:0',
            'total_rachas' => 'sometimes|integer|min:0',
            'vitorias' => 'sometimes|integer|min:0',
            'derrotas' => 'sometimes|integer|min:0',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'nivel.in' => 'O nível deve ser: iniciante, intermediario, avancado ou profissional.',
            'lado_preferido.in' => 'O lado preferido deve ser: esquerda, direita ou ambos.',
            'nivel_jogo.in' => 'O nível de jogo deve ser: iniciante, intermediario, avancado ou profissional.',
            'ranking.min' => 'O ranking não pode ser negativo.',
            'total_rachas.min' => 'O total de rachas não pode ser negativo.',
            'vitorias.min' => 'O total de vitórias não pode ser negativo.',
            'derrotas.min' => 'O total de derrotas não pode ser negativo.',
        ];
    }
}
