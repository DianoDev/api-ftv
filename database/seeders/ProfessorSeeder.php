<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProfessorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $professores = [];

        // Professores são os usuários de ID 29 a 33
        for ($i = 1; $i <= 5; $i++) {
            $experiencia = rand(3, 15);
            
            $professores[] = [
                'user_id' => 28 + $i,
                'certificacoes' => json_encode([
                    'CBT - Confederação Brasileira de Tênis',
                    'ITF Beach Tennis Coach Level ' . rand(1, 3),
                    'Curso de Primeiros Socorros'
                ]),
                'experiencia_anos' => $experiencia,
                'especialidades' => json_encode([
                    'Iniciantes',
                    'Técnica de Saque',
                    'Jogadas de Rede',
                    'Estratégia de Jogo'
                ]),
                'valor_hora_aula' => rand(80, 200),
                'descricao' => "Professor(a) com {$experiencia} anos de experiência em beach tennis, focado em desenvolvimento técnico e tático dos alunos.",
                'disponibilidade' => json_encode([
                    'segunda' => ['08:00-12:00', '14:00-18:00'],
                    'terca' => ['08:00-12:00', '14:00-18:00'],
                    'quarta' => ['08:00-12:00', '14:00-18:00'],
                    'quinta' => ['08:00-12:00', '14:00-18:00'],
                    'sexta' => ['08:00-12:00', '14:00-18:00'],
                    'sabado' => ['08:00-12:00']
                ]),
                'rating' => rand(40, 50) / 10,
                'total_avaliacoes' => rand(15, 80),
                'ativo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('professores')->insert($professores);
    }
}
