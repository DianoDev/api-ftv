<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RachaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        // Criar alguns rachas finalizados
        $rachas = [];
        $duplas = [];
        $sets = [];
        $pontos = [];
        $estatisticas = [];

        // Racha 1 - Finalizado
        $rachas[] = [
            'id' => 1,
            'criador_id' => 1,
            'quadra_id' => 1,
            'juiz_id' => null,
            'data_racha' => $now->copy()->subDays(5)->format('Y-m-d H:i:s'),
            'duracao_minutos' => 90,
            'valor_total' => 200.00,
            'pagamento_status' => 'pago',
            'status' => 'finalizado',
            'dupla_vencedora' => 1,
            'placar_dupla1' => 2,
            'placar_dupla2' => 1,
            'nivel_sugerido' => 'intermediario',
            'finalizado_at' => $now->copy()->subDays(5)->addMinutes(90),
            'created_at' => $now->copy()->subDays(5),
            'updated_at' => $now->copy()->subDays(5)->addMinutes(90),
        ];

        // Duplas do Racha 1
        $duplas[] = [
            'id' => 1,
            'racha_id' => 1,
            'numero_dupla' => 1,
            'jogador1_id' => 1,
            'jogador2_id' => 2,
            'pontos_ranking_ganhos' => 25,
            'created_at' => $now->copy()->subDays(5),
            'updated_at' => $now->copy()->subDays(5),
        ];

        $duplas[] = [
            'id' => 2,
            'racha_id' => 1,
            'numero_dupla' => 2,
            'jogador1_id' => 3,
            'jogador2_id' => 4,
            'pontos_ranking_ganhos' => -15,
            'created_at' => $now->copy()->subDays(5),
            'updated_at' => $now->copy()->subDays(5),
        ];

        // Sets do Racha 1
        $sets[] = [
            'id' => 1,
            'racha_id' => 1,
            'numero_set' => 1,
            'pontos_dupla1' => 9,
            'pontos_dupla2' => 7,
            'dupla_vencedora' => 1,
            'status' => 'finalizado',
            'iniciado_em' => $now->copy()->subDays(5),
            'finalizado_em' => $now->copy()->subDays(5)->addMinutes(25),
            'created_at' => $now->copy()->subDays(5),
            'updated_at' => $now->copy()->subDays(5)->addMinutes(25),
        ];

        $sets[] = [
            'id' => 2,
            'racha_id' => 1,
            'numero_set' => 2,
            'pontos_dupla1' => 7,
            'pontos_dupla2' => 9,
            'dupla_vencedora' => 2,
            'status' => 'finalizado',
            'iniciado_em' => $now->copy()->subDays(5)->addMinutes(25),
            'finalizado_em' => $now->copy()->subDays(5)->addMinutes(55),
            'created_at' => $now->copy()->subDays(5)->addMinutes(25),
            'updated_at' => $now->copy()->subDays(5)->addMinutes(55),
        ];

        $sets[] = [
            'id' => 3,
            'racha_id' => 1,
            'numero_set' => 3,
            'pontos_dupla1' => 9,
            'pontos_dupla2' => 5,
            'dupla_vencedora' => 1,
            'status' => 'finalizado',
            'iniciado_em' => $now->copy()->subDays(5)->addMinutes(55),
            'finalizado_em' => $now->copy()->subDays(5)->addMinutes(90),
            'created_at' => $now->copy()->subDays(5)->addMinutes(55),
            'updated_at' => $now->copy()->subDays(5)->addMinutes(90),
        ];

        // Estatísticas do Racha 1
        $estatisticas[] = [
            'racha_id' => 1,
            'jogador_id' => 1,
            'dupla_id' => 1,
            'sets_ganhos' => 2,
            'sets_perdidos' => 1,
            'pontos_feitos' => 25,
            'pontos_sofridos' => 21,
            'aces' => 4,
            'ataques_ponto' => 8,
            'bloqueios' => 3,
            'erros_saque' => 2,
            'erros_ataque' => 3,
            'aproveitamento' => 75.50,
            'created_at' => $now->copy()->subDays(5),
            'updated_at' => $now->copy()->subDays(5),
        ];

        $estatisticas[] = [
            'racha_id' => 1,
            'jogador_id' => 2,
            'dupla_id' => 1,
            'sets_ganhos' => 2,
            'sets_perdidos' => 1,
            'pontos_feitos' => 25,
            'pontos_sofridos' => 21,
            'aces' => 3,
            'ataques_ponto' => 7,
            'bloqueios' => 4,
            'erros_saque' => 1,
            'erros_ataque' => 2,
            'aproveitamento' => 78.30,
            'created_at' => $now->copy()->subDays(5),
            'updated_at' => $now->copy()->subDays(5),
        ];

        $estatisticas[] = [
            'racha_id' => 1,
            'jogador_id' => 3,
            'dupla_id' => 2,
            'sets_ganhos' => 1,
            'sets_perdidos' => 2,
            'pontos_feitos' => 21,
            'pontos_sofridos' => 25,
            'aces' => 2,
            'ataques_ponto' => 6,
            'bloqueios' => 2,
            'erros_saque' => 3,
            'erros_ataque' => 4,
            'aproveitamento' => 65.20,
            'created_at' => $now->copy()->subDays(5),
            'updated_at' => $now->copy()->subDays(5),
        ];

        $estatisticas[] = [
            'racha_id' => 1,
            'jogador_id' => 4,
            'dupla_id' => 2,
            'sets_ganhos' => 1,
            'sets_perdidos' => 2,
            'pontos_feitos' => 21,
            'pontos_sofridos' => 25,
            'aces' => 3,
            'ataques_ponto' => 5,
            'bloqueios' => 3,
            'erros_saque' => 2,
            'erros_ataque' => 3,
            'aproveitamento' => 68.50,
            'created_at' => $now->copy()->subDays(5),
            'updated_at' => $now->copy()->subDays(5),
        ];

        // Racha 2 - Agendado para amanhã
        $rachas[] = [
            'id' => 2,
            'criador_id' => 5,
            'quadra_id' => 2,
            'juiz_id' => null,
            'data_racha' => $now->copy()->addDay()->setTime(18, 0, 0)->format('Y-m-d H:i:s'),
            'duracao_minutos' => 60,
            'valor_total' => 160.00,
            'pagamento_status' => 'pendente',
            'status' => 'confirmado',
            'nivel_sugerido' => 'avancado',
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $duplas[] = [
            'id' => 3,
            'racha_id' => 2,
            'numero_dupla' => 1,
            'jogador1_id' => 5,
            'jogador2_id' => 6,
            'pontos_ranking_ganhos' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $duplas[] = [
            'id' => 4,
            'racha_id' => 2,
            'numero_dupla' => 2,
            'jogador1_id' => 7,
            'jogador2_id' => 8,
            'pontos_ranking_ganhos' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        // Racha 3 - Finalizado ontem
        $rachas[] = [
            'id' => 3,
            'criador_id' => 9,
            'quadra_id' => 3,
            'juiz_id' => null,
            'data_racha' => $now->copy()->subDay()->format('Y-m-d H:i:s'),
            'duracao_minutos' => 75,
            'valor_total' => 180.00,
            'pagamento_status' => 'pago',
            'status' => 'finalizado',
            'dupla_vencedora' => 2,
            'placar_dupla1' => 1,
            'placar_dupla2' => 2,
            'nivel_sugerido' => 'intermediario',
            'finalizado_at' => $now->copy()->subDay()->addMinutes(75),
            'created_at' => $now->copy()->subDay(),
            'updated_at' => $now->copy()->subDay()->addMinutes(75),
        ];

        $duplas[] = [
            'id' => 5,
            'racha_id' => 3,
            'numero_dupla' => 1,
            'jogador1_id' => 9,
            'jogador2_id' => 10,
            'pontos_ranking_ganhos' => -10,
            'created_at' => $now->copy()->subDay(),
            'updated_at' => $now->copy()->subDay(),
        ];

        $duplas[] = [
            'id' => 6,
            'racha_id' => 3,
            'numero_dupla' => 2,
            'jogador1_id' => 11,
            'jogador2_id' => 12,
            'pontos_ranking_ganhos' => 20,
            'created_at' => $now->copy()->subDay(),
            'updated_at' => $now->copy()->subDay(),
        ];

        // Inserir dados
        DB::table('rachas')->insert($rachas);
        DB::table('duplas_racha')->insert($duplas);
        DB::table('sets_racha')->insert($sets);
        DB::table('estatisticas_jogador_racha')->insert($estatisticas);

        // Criar solicitações de racha
        $solicitacoes = [
            [
                'criador_id' => 13,
                'arena_id' => 1,
                'data_jogo' => $now->copy()->addDays(3)->format('Y-m-d'),
                'hora_inicio' => '19:00:00',
                'hora_fim' => '20:30:00',
                'duracao_horas' => 1.5,
                'limite_participantes' => 4,
                'participantes_atuais' => 2,
                'valor_estimado' => 180.00,
                'valor_por_pessoa' => 45.00,
                'status' => 'aberta',
                'nivel_sugerido' => 'intermediario',
                'descricao' => 'Procurando mais 2 jogadores para fechar o racha!',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'criador_id' => 14,
                'arena_id' => 2,
                'data_jogo' => $now->copy()->addDays(7)->format('Y-m-d'),
                'hora_inicio' => '18:00:00',
                'hora_fim' => '19:00:00',
                'duracao_horas' => 1.0,
                'limite_participantes' => 4,
                'participantes_atuais' => 1,
                'valor_estimado' => 140.00,
                'valor_por_pessoa' => 35.00,
                'status' => 'aberta',
                'nivel_sugerido' => 'avancado',
                'descricao' => 'Racha de nível avançado. Vamos jogar pesado!',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('solicitacoes_racha')->insert($solicitacoes);

        // Criar procura de parceiros
        $procuras = [
            [
                'criador_id' => 15,
                'quadra_id' => 1,
                'data_jogo' => $now->copy()->addDays(2)->format('Y-m-d'),
                'hora_jogo' => '17:00:00',
                'nivel_desejado' => 'intermediario',
                'lado_desejado' => 'esquerda',
                'vagas' => 1,
                'tipo_busca' => 'parceiro',
                'descricao' => 'Procuro parceiro para treino. Jogo de fundo.',
                'status' => 'aberto',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'criador_id' => 16,
                'quadra_id' => 2,
                'data_jogo' => $now->copy()->addDays(5)->format('Y-m-d'),
                'hora_jogo' => '19:30:00',
                'nivel_desejado' => 'avancado',
                'lado_desejado' => 'ambos',
                'vagas' => 3,
                'tipo_busca' => 'adversarios',
                'descricao' => 'Eu e meu parceiro procuramos adversários para jogar.',
                'status' => 'aberto',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('procura_parceiros')->insert($procuras);

        // Criar histórico de ranking
        $historicoRanking = [
            [
                'usuario_id' => 1,
                'racha_id' => 1,
                'ranking_anterior' => 1200,
                'ranking_novo' => 1225,
                'diferenca' => 25,
                'motivo' => 'vitoria_racha',
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5),
            ],
            [
                'usuario_id' => 2,
                'racha_id' => 1,
                'ranking_anterior' => 1180,
                'ranking_novo' => 1205,
                'diferenca' => 25,
                'motivo' => 'vitoria_racha',
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5),
            ],
            [
                'usuario_id' => 3,
                'racha_id' => 1,
                'ranking_anterior' => 1150,
                'ranking_novo' => 1135,
                'diferenca' => -15,
                'motivo' => 'derrota_racha',
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5),
            ],
            [
                'usuario_id' => 4,
                'racha_id' => 1,
                'ranking_anterior' => 1170,
                'ranking_novo' => 1155,
                'diferenca' => -15,
                'motivo' => 'derrota_racha',
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5),
            ],
        ];

        DB::table('historico_ranking')->insert($historicoRanking);

        // Criar notificações
        $notificacoes = [
            [
                'usuario_id' => 1,
                'tipo' => 'racha_confirmado',
                'titulo' => 'Racha Confirmado',
                'mensagem' => 'Seu racha para amanhã às 18h foi confirmado!',
                'dados' => json_encode(['racha_id' => 2]),
                'lida' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'usuario_id' => 2,
                'tipo' => 'resultado_racha',
                'titulo' => 'Vitória no Racha!',
                'mensagem' => 'Parabéns! Sua dupla venceu o racha. +25 pontos no ranking.',
                'dados' => json_encode(['racha_id' => 1, 'pontos' => 25]),
                'lida' => true,
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5),
            ],
            [
                'usuario_id' => 13,
                'tipo' => 'interesse_solicitacao',
                'titulo' => 'Novo Interesse',
                'mensagem' => 'Um jogador demonstrou interesse na sua solicitação de racha.',
                'dados' => json_encode(['solicitacao_id' => 1]),
                'lida' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('notificacoes')->insert($notificacoes);

        // Criar reservas
        $reservas = [
            [
                'quadra_id' => 1,
                'usuario_id' => 1,
                'data_reserva' => $now->copy()->addDays(2)->format('Y-m-d'),
                'hora_inicio' => '18:00:00',
                'hora_fim' => '19:00:00',
                'valor' => 120.00,
                'status' => 'confirmada',
                'pagamento_confirmado' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quadra_id' => 2,
                'usuario_id' => 5,
                'data_reserva' => $now->copy()->addDays(4)->format('Y-m-d'),
                'hora_inicio' => '19:00:00',
                'hora_fim' => '20:30:00',
                'valor' => 180.00,
                'status' => 'pendente',
                'pagamento_confirmado' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('reservas')->insert($reservas);
    }
}
