<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $posts = [];
        $comentarios = [];
        $curtidas = [];

        $conteudos = [
            "Que jogo incrível hoje! 🏐 Muito orgulhoso da evolução!",
            "Procurando parceiro para treino amanhã às 18h. Quem topa? 💪",
            "Primeiro torneio da temporada! Vamos com tudo! 🏆",
            "Dica: treinar saque é fundamental! Dediquem 30min por dia só nisso.",
            "Agradecendo os amigos pelo racha de hoje. Que dia especial! 🙌",
            "Melhor investimento: aulas com um bom professor. Vale cada centavo!",
            "Vitória suada hoje! Time adversário jogou muito bem 👏",
            "Alguém mais viciado nesse esporte? Não consigo parar! 😄",
            "Arena nova na cidade! Alguém já foi conferir?",
            "Final de semana chegando... Já sabem o que fazer! 🏖️🏐",
            "Perdemos hoje mas aprendemos muito. Bora evoluir! 💪",
            "Obrigado pelos 1000 seguidores! Conteúdo de beach tennis sempre!",
            "Tutorial de bloqueio no meu perfil! Confere lá!",
            "Quem quer participar do torneio beneficente? Inscrições abertas!",
            "Melhor sensação: voleio perfeito na rede! 🔥",
        ];

        // Criar posts para os primeiros 15 jogadores
        for ($i = 1; $i <= 15; $i++) {
            $numPosts = rand(1, 3);
            
            for ($p = 0; $p < $numPosts; $p++) {
                $postId = count($posts) + 1;
                $diasAtras = rand(1, 30);
                $createdAt = $now->copy()->subDays($diasAtras);
                
                $posts[] = [
                    'usuario_id' => $i,
                    'conteudo' => $conteudos[array_rand($conteudos)],
                    'imagem' => rand(0, 1) ? "https://exemplo.com/post_image_{$postId}.jpg" : null,
                    'expira_em' => $createdAt->copy()->addHours(24),
                    'ativo' => $diasAtras < 1, // Apenas posts de hoje ficam ativos
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];

                // Adicionar alguns comentários
                $numComentarios = rand(0, 5);
                for ($c = 0; $c < $numComentarios; $c++) {
                    $comentarios[] = [
                        'post_id' => $postId,
                        'usuario_id' => rand(1, 20),
                        'conteudo' => $this->getComentario(),
                        'created_at' => $createdAt->copy()->addMinutes(rand(10, 300)),
                        'updated_at' => $createdAt->copy()->addMinutes(rand(10, 300)),
                    ];
                }

                // Adicionar curtidas
                $numCurtidas = rand(5, 15);
                $usuariosCurtiram = [];
                for ($l = 0; $l < $numCurtidas; $l++) {
                    $usuarioId = rand(1, 20);
                    if (!in_array($usuarioId, $usuariosCurtiram)) {
                        $usuariosCurtiram[] = $usuarioId;
                        $curtidas[] = [
                            'post_id' => $postId,
                            'usuario_id' => $usuarioId,
                            'created_at' => $createdAt->copy()->addMinutes(rand(5, 200)),
                            'updated_at' => $createdAt->copy()->addMinutes(rand(5, 200)),
                        ];
                    }
                }
            }
        }

        DB::table('posts')->insert($posts);
        if (!empty($comentarios)) {
            DB::table('comentarios')->insert($comentarios);
        }
        if (!empty($curtidas)) {
            DB::table('curtidas')->insert($curtidas);
        }
    }

    private function getComentario()
    {
        $comentarios = [
            "Parabéns! 👏",
            "Show de bola!",
            "Bora treinar junto!",
            "Que jogo foi esse! 🔥",
            "Topo sim!",
            "Inspirador demais!",
            "Também quero participar!",
            "Boa sorte no torneio!",
            "Vamos juntos!",
            "Concordo totalmente!",
        ];
        return $comentarios[array_rand($comentarios)];
    }
}
