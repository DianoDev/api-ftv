<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ArenaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $arenas = [];

        $nomes = [
            'Arena Beach Cuiabá',
            'VG Sports Arena',
            'Rondon Beach Club',
            'Sinop Beach Center',
            'Tangará Sports Complex',
            'Lucas Beach Paradise',
            'Sorriso Beach Arena',
            'Primavera Beach Club'
        ];

        $enderecos = [
            'Av. Miguel Sutil, 8000',
            'Av. da FEB, 1500',
            'Rua Barão do Melgaço, 2000',
            'Av. dos Tarumãs, 500',
            'Rua Marechal Rondon, 1200',
            'Av. América do Sul, 800',
            'Rua das Primaveras, 1000',
            'Av. Cuiabá, 2500'
        ];

        $cidades = [
            'Cuiabá',
            'Várzea Grande',
            'Rondonópolis',
            'Sinop',
            'Tangará da Serra',
            'Lucas do Rio Verde',
            'Sorriso',
            'Primavera do Leste'
        ];

        // Proprietários das arenas são os usuários de ID 21 a 28
        for ($i = 0; $i < 8; $i++) {
            $arenas[] = [
                'proprietario_id' => 21 + $i,
                'nome' => $nomes[$i],
                'descricao' => "Arena completa de beach tennis com quadras de areia de alta qualidade, vestiários, estacionamento e área de convivência.",
                'cnpj' => sprintf('%02d.%03d.%03d/%04d-%02d', rand(10, 99), rand(100, 999), rand(100, 999), rand(1000, 9999), rand(10, 99)),
                'endereco' => $enderecos[$i],
                'cidade' => $cidades[$i],
                'estado' => 'MT',
                'cep' => sprintf('%05d-%03d', rand(78000, 78999), rand(100, 999)),
                'latitude' => -15.6000 + (rand(-1000, 1000) / 10000),
                'longitude' => -56.1000 + (rand(-1000, 1000) / 10000),
                'telefone' => sprintf('(65) 3%03d-%04d', rand(100, 999), rand(1000, 9999)),
                'whatsapp' => sprintf('(65) 9%04d-%04d', rand(8000, 9999), rand(1000, 9999)),
                'fotos' => json_encode([
                    "https://exemplo.com/arena{$i}_1.jpg",
                    "https://exemplo.com/arena{$i}_2.jpg",
                    "https://exemplo.com/arena{$i}_3.jpg"
                ]),
                'horario_funcionamento' => json_encode([
                    'segunda' => '06:00-22:00',
                    'terca' => '06:00-22:00',
                    'quarta' => '06:00-22:00',
                    'quinta' => '06:00-22:00',
                    'sexta' => '06:00-23:00',
                    'sabado' => '07:00-23:00',
                    'domingo' => '07:00-20:00'
                ]),
                'comodidades' => json_encode([
                    'estacionamento',
                    'vestiarios',
                    'chuveiros',
                    'lanchonete',
                    'wifi',
                    'iluminacao_noturna',
                    'loja_equipamentos'
                ]),
                'rating' => rand(35, 50) / 10,
                'total_avaliacoes' => rand(10, 100),
                'ativo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('arenas')->insert($arenas);
    }
}
