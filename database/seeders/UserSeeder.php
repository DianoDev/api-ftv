<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $password = '$2y$12$mMjWOPu5lwPcX.Jq4pyI/ejv/1T0/.1S0aEYethtFX9X5DlKvrVcq'; // senha: password

        // Criar 20 Jogadores
        $jogadores = [];
        for ($i = 1; $i <= 20; $i++) {
            $jogadores[] = [
                'nome' => "Jogador {$i}",
                'email' => "jogador{$i}@gmail.com",
                'password' => $password,
                'tipo_usuario' => 'jogador',
                'telefone' => '(65) 9' . str_pad($i, 4, '0', STR_PAD_LEFT) . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'cidade' => $this->getCidade($i),
                'estado' => $this->getEstado($i),
                'data_nascimento' => Carbon::now()->subYears(rand(18, 45))->format('Y-m-d'),
                'genero' => $i % 2 == 0 ? 'masculino' : 'feminino',
                'bio' => "Jogador(a) de beach tennis nível " . $this->getNivel($i),
                'rating' => rand(30, 50) / 10,
                'total_avaliacoes' => rand(5, 50),
                'perfil_completo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('users')->insert($jogadores);

        // Criar 8 Arenas (proprietários)
        $arenas = [];
        for ($i = 1; $i <= 8; $i++) {
            $arenas[] = [
                'nome' => "Arena {$i}",
                'email' => "arena{$i}@gmail.com",
                'password' => $password,
                'tipo_usuario' => 'arena',
                'telefone' => '(65) 3' . str_pad($i, 3, '0', STR_PAD_LEFT) . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'cidade' => $this->getCidadeArena($i),
                'estado' => 'MT',
                'bio' => "Arena de beach tennis com infraestrutura completa",
                'rating' => rand(35, 50) / 10,
                'total_avaliacoes' => rand(10, 100),
                'perfil_completo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('users')->insert($arenas);

        // Criar 5 Professores
        $professores = [];
        for ($i = 1; $i <= 5; $i++) {
            $anosExperiencia = $i + 3;
            $professores[] = [
                'nome' => "Professor {$i}",
                'email' => "professor{$i}@gmail.com",
                'password' => $password,
                'tipo_usuario' => 'professor',
                'telefone' => '(65) 9' . str_pad(90 + $i, 4, '0', STR_PAD_LEFT) . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'cidade' => 'Cuiabá',
                'estado' => 'MT',
                'data_nascimento' => Carbon::now()->subYears(rand(25, 50))->format('Y-m-d'),
                'genero' => $i % 2 == 0 ? 'masculino' : 'feminino',
                'bio' => "Professor com {$anosExperiencia} anos de experiência",
                'rating' => rand(40, 50) / 10,
                'total_avaliacoes' => rand(15, 80),
                'perfil_completo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('users')->insert($professores);
    }

    private function getCidade($index)
    {
        $cidades = ['Cuiabá', 'Várzea Grande', 'Rondonópolis', 'Sinop', 'Tangará da Serra'];
        return $cidades[$index % count($cidades)];
    }

    private function getEstado($index)
    {
        $estados = ['MT', 'MT', 'MT', 'SP', 'RJ', 'MG', 'PR', 'SC', 'RS', 'GO'];
        return $estados[$index % count($estados)];
    }

    private function getCidadeArena($index)
    {
        $cidades = ['Cuiabá', 'Várzea Grande', 'Rondonópolis', 'Sinop', 'Tangará da Serra', 'Lucas do Rio Verde', 'Sorriso', 'Primavera do Leste'];
        return $cidades[$index - 1];
    }

    private function getNivel($index)
    {
        $niveis = ['iniciante', 'intermediário', 'avançado', 'profissional'];
        return $niveis[$index % count($niveis)];
    }
}
