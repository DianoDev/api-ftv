<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AmizadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $amizades = [];

        // Criar amizades entre jogadores (IDs 1-20)
        $amizadesData = [
            [1, 2, 'aceito'],
            [1, 3, 'aceito'],
            [1, 5, 'pendente'],
            [2, 4, 'aceito'],
            [2, 6, 'aceito'],
            [3, 4, 'aceito'],
            [3, 7, 'aceito'],
            [4, 8, 'aceito'],
            [5, 6, 'aceito'],
            [5, 9, 'pendente'],
            [6, 10, 'aceito'],
            [7, 8, 'aceito'],
            [7, 11, 'aceito'],
            [8, 12, 'aceito'],
            [9, 10, 'aceito'],
            [9, 13, 'aceito'],
            [10, 14, 'aceito'],
            [11, 12, 'aceito'],
            [11, 15, 'pendente'],
            [12, 16, 'aceito'],
            [13, 14, 'aceito'],
            [13, 17, 'aceito'],
            [14, 18, 'aceito'],
            [15, 16, 'aceito'],
            [15, 19, 'aceito'],
            [16, 20, 'aceito'],
            [17, 18, 'aceito'],
            [17, 1, 'aceito'],
            [18, 2, 'aceito'],
            [19, 20, 'aceito'],
        ];

        foreach ($amizadesData as $amizade) {
            $aceito = $amizade[2] === 'aceito';
            
            $amizades[] = [
                'usuario_id' => $amizade[0],
                'amigo_id' => $amizade[1],
                'status' => $amizade[2],
                'aceito_em' => $aceito ? Carbon::now()->subDays(rand(1, 30)) : null,
                'created_at' => $now->copy()->subDays(rand(1, 60)),
                'updated_at' => $now,
            ];
        }

        DB::table('amizades')->insert($amizades);
    }
}
