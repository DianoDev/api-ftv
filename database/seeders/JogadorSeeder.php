<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JogadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $jogadores = [];

        // Pegar os IDs dos usuários jogadores (primeiros 20)
        for ($i = 1; $i <= 20; $i++) {
            $nivel = $this->getNivel($i);
            $ranking = $this->getRanking($nivel, $i);
            
            $jogadores[] = [
                'user_id' => $i,
                'nivel' => $nivel,
                'lado_preferido' => $this->getLadoPreferido($i),
                'ranking' => $ranking,
                'total_rachas' => rand(5, 100),
                'vitorias' => rand(2, 60),
                'derrotas' => rand(2, 40),
                'posicao_preferida' => $this->getPosicao($i),
                'nivel_jogo' => $nivel,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('jogadores')->insert($jogadores);
    }

    private function getNivel($index)
    {
        $niveis = ['iniciante', 'intermediario', 'avancado', 'profissional'];
        if ($index <= 5) return 'iniciante';
        if ($index <= 12) return 'intermediario';
        if ($index <= 18) return 'avancado';
        return 'profissional';
    }

    private function getRanking($nivel, $index)
    {
        switch ($nivel) {
            case 'iniciante':
                return rand(800, 1000);
            case 'intermediario':
                return rand(1000, 1300);
            case 'avancado':
                return rand(1300, 1600);
            case 'profissional':
                return rand(1600, 2000);
            default:
                return 1000;
        }
    }

    private function getLadoPreferido($index)
    {
        $lados = ['direita', 'esquerda', 'ambos'];
        return $lados[$index % count($lados)];
    }

    private function getPosicao($index)
    {
        $posicoes = ['fundo', 'rede', 'ambas'];
        return $posicoes[$index % count($posicoes)];
    }
}
