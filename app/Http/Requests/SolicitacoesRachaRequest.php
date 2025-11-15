<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SolicitacoesRachaRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'arena_id' => 'required|exists:arenas,id',
            'data_jogo' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'required|date_format:H:i|after:hora_inicio',
            'limite_participantes' => 'required|integer|min:2|max:100',
            'valor_estimado' => 'nullable|numeric|min:0',
            'valor_por_pessoa' => 'nullable|numeric|min:0',
            'nivel_sugerido' => 'nullable|string|in:iniciante,intermediario,avancado,profissional,misto',
            'descricao' => 'nullable|string|max:500',
            'observacoes' => 'nullable|string|max:1000',
            'duracao_horas' => 'nullable|numeric|min:0.5|max:24',
        ];

        // Se estiver atualizando, alguns campos não são obrigatórios
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['arena_id'] = 'sometimes|exists:arenas,id';
            $rules['data_jogo'] = 'sometimes|date|after_or_equal:today';
            $rules['hora_inicio'] = 'sometimes|date_format:H:i';
            $rules['hora_fim'] = 'sometimes|date_format:H:i|after:hora_inicio';
            $rules['limite_participantes'] = 'sometimes|integer|min:2|max:100';
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
            'arena_id.required' => 'A arena é obrigatória.',
            'arena_id.exists' => 'A arena selecionada não existe.',
            'data_jogo.required' => 'A data do jogo é obrigatória.',
            'data_jogo.after_or_equal' => 'A data do jogo deve ser hoje ou no futuro.',
            'hora_inicio.required' => 'A hora de início é obrigatória.',
            'hora_inicio.date_format' => 'A hora de início deve estar no formato HH:MM.',
            'hora_fim.required' => 'A hora de fim é obrigatória.',
            'hora_fim.date_format' => 'A hora de fim deve estar no formato HH:MM.',
            'hora_fim.after' => 'A hora de fim deve ser posterior à hora de início.',
            'limite_participantes.required' => 'O limite de participantes é obrigatório.',
            'limite_participantes.min' => 'Deve haver no mínimo 2 participantes.',
            'limite_participantes.max' => 'O máximo de participantes é 100.',
            'valor_estimado.numeric' => 'O valor estimado deve ser numérico.',
            'valor_estimado.min' => 'O valor estimado não pode ser negativo.',
            'valor_por_pessoa.numeric' => 'O valor por pessoa deve ser numérico.',
            'valor_por_pessoa.min' => 'O valor por pessoa não pode ser negativo.',
            'nivel_sugerido.in' => 'O nível sugerido deve ser: iniciante, intermediario, avancado, profissional ou misto.',
            'duracao_horas.min' => 'A duração mínima é de 30 minutos (0.5 horas).',
            'duracao_horas.max' => 'A duração máxima é de 24 horas.',
        ];
    }
}
