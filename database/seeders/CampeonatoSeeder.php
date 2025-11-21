<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CampeonatoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Criar 3 campeonatos
        $campeonatos = [
            [
                'id' => 1,
                'organizador_id' => 1, // Jogador 1 organizando
                'arena_id' => 1,
                'nome' => 'Circuito Cuiabá Beach Tennis 2025',
                'descricao' => 'Primeiro torneio oficial do circuito regional de beach tennis.',
                'data_inicio' => $now->copy()->addDays(15)->format('Y-m-d'),
                'data_fim' => $now->copy()->addDays(17)->format('Y-m-d'),
                'tipo' => 'eliminatorias',
                'regras' => 'Formato eliminação simples. Melhor de 3 sets. Tie-break em 6x6.',
                'status' => 'inscricoes_abertas',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'organizador_id' => 21, // Arena 1
                'arena_id' => 2,
                'nome' => 'Copa VG Beach Tennis',
                'descricao' => 'Torneio aberto para todas as categorias.',
                'data_inicio' => $now->copy()->addDays(30)->format('Y-m-d'),
                'data_fim' => $now->copy()->addDays(32)->format('Y-m-d'),
                'tipo' => 'eliminatorias_com_repescagem',
                'regras' => 'Formato grupos + eliminatórias. Melhor de 3 sets.',
                'status' => 'inscricoes_abertas',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'organizador_id' => 2,
                'arena_id' => 3,
                'nome' => 'Rondon Beach Open',
                'descricao' => 'Torneio tradicional de Rondonópolis.',
                'data_inicio' => $now->copy()->addDays(45)->format('Y-m-d'),
                'data_fim' => $now->copy()->addDays(47)->format('Y-m-d'),
                'tipo' => 'eliminatorias_com_repescagem',
                'regras' => 'Eliminação simples. Sets de 9 games.',
                'status' => 'inscricoes_abertas',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('campeonatos')->insert($campeonatos);

        // Criar categorias para cada campeonato
        $categorias = [
            // Campeonato 1
            [
                'campeonato_id' => 1,
                'nome' => 'Masculino A',
                'genero' => 'masculino',
                'nivel' => 'avancado',
                'tipo_inscricao' => 'solo',
                'max_duplas' => 16,
                'valor_inscricao' => 150.00,
                'premiacao' => json_encode([
                    '1º lugar' => 'R$ 2.000,00',
                    '2º lugar' => 'R$ 1.000,00',
                    '3º lugar' => 'R$ 500,00'
                ]),
                'status' => 'inscricoes_abertas',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'campeonato_id' => 1,
                'nome' => 'Feminino A',
                'genero' => 'feminino',
                'nivel' => 'avancado',
                'tipo_inscricao' => 'dupla',
                'max_duplas' => 16,
                'valor_inscricao' => 150.00,
                'premiacao' => json_encode([
                    '1º lugar' => 'R$ 2.000,00',
                    '2º lugar' => 'R$ 1.000,00',
                    '3º lugar' => 'R$ 500,00'
                ]),
                'status' => 'inscricoes_abertas',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'campeonato_id' => 1,
                'nome' => 'Misto A',
                'genero' => 'misto',
                'nivel' => 'avancado',
                'tipo_inscricao' => 'solo',
                'max_duplas' => 12,
                'valor_inscricao' => 150.00,
                'premiacao' => json_encode([
                    '1º lugar' => 'R$ 1.500,00',
                    '2º lugar' => 'R$ 750,00'
                ]),
                'status' => 'inscricoes_abertas',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Campeonato 2
            [
                'campeonato_id' => 2,
                'nome' => 'Masculino B',
                'genero' => 'masculino',
                'nivel' => 'intermediario',
                'tipo_inscricao' => 'solo',
                'max_duplas' => 16,
                'valor_inscricao' => 100.00,
                'premiacao' => json_encode([
                    '1º lugar' => 'R$ 1.000,00',
                    '2º lugar' => 'R$ 500,00'
                ]),
                'status' => 'inscricoes_abertas',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'campeonato_id' => 2,
                'nome' => 'Feminino B',
                'genero' => 'feminino',
                'nivel' => 'intermediario',
                'tipo_inscricao' => 'dupla',
                'max_duplas' => 12,
                'valor_inscricao' => 100.00,
                'premiacao' => json_encode([
                    '1º lugar' => 'R$ 1.000,00',
                    '2º lugar' => 'R$ 500,00'
                ]),
                'status' => 'inscricoes_abertas',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Campeonato 3
            [
                'campeonato_id' => 3,
                'nome' => 'Iniciante',
                'genero' => 'misto',
                'nivel' => 'iniciante',
                'tipo_inscricao' => 'solo',
                'max_duplas' => 8,
                'valor_inscricao' => 80.00,
                'premiacao' => json_encode([
                    '1º lugar' => 'Troféu + Kit',
                    '2º lugar' => 'Medalha'
                ]),
                'status' => 'inscricoes_abertas',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('categorias_campeonato')->insert($categorias);

        // Criar algumas inscrições
        $inscricoes = [];

        // Categoria 1 - Masculino A (Campeonato 1)
        $duplasMasculinoA = [
            [1, 3], [5, 7], [9, 11], [13, 15], [17, 19], [2, 4]
        ];

        foreach ($duplasMasculinoA as $dupla) {
            $inscricoes[] = [
                'categoria_id' => 1,
                'jogador1_id' => $dupla[0],
                'jogador2_id' => $dupla[1],
                'nome_equipe' => "Dupla {$dupla[0]}/{$dupla[1]}",
                'status' => 'confirmada',
                'pagamento_confirmado' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Categoria 2 - Feminino A
        $duplasFemininoA = [
            [2, 4], [6, 8], [10, 12], [14, 16]
        ];

        foreach ($duplasFemininoA as $dupla) {
            $inscricoes[] = [
                'categoria_id' => 2,
                'jogador1_id' => $dupla[0],
                'jogador2_id' => $dupla[1],
                'nome_equipe' => "Dupla {$dupla[0]}/{$dupla[1]}",
                'status' => 'confirmada',
                'pagamento_confirmado' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Categoria 4 - Masculino B (Campeonato 2)
        $duplasMasculinoB = [
            [1, 2], [3, 4], [5, 6], [7, 8]
        ];

        foreach ($duplasMasculinoB as $dupla) {
            $inscricoes[] = [
                'categoria_id' => 4,
                'jogador1_id' => $dupla[0],
                'jogador2_id' => $dupla[1],
                'nome_equipe' => "Equipe {$dupla[0]}/{$dupla[1]}",
                'status' => 'pendente',
                'pagamento_confirmado' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('inscricoes_campeonato')->insert($inscricoes);
    }
}
