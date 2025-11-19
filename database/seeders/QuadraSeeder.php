<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuadraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $quadras = [];

        // Cada arena terá de 2 a 4 quadras
        $quadrasPorArena = [4, 3, 4, 2, 3, 2, 4, 3];

        for ($arenaId = 1; $arenaId <= 8; $arenaId++) {
            $numQuadras = $quadrasPorArena[$arenaId - 1];
            
            for ($q = 1; $q <= $numQuadras; $q++) {
                $coberta = $q <= 2; // Primeiras 2 quadras são cobertas
                $valorBase = rand(80, 150);
                
                $quadras[] = [
                    'arena_id' => $arenaId,
                    'nome' => "Quadra {$q}",
                    'comprimento' => '16m',
                    'largura' => '8m',
                    'valor_hora' => $valorBase + ($coberta ? 20 : 0),
                    'coberta' => $coberta,
                    'iluminacao' => true,
                    'ativa' => true,
                    'observacoes' => $coberta ? 'Quadra coberta, ideal para dias de chuva' : 'Quadra ao ar livre',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('quadras')->insert($quadras);
    }
}
