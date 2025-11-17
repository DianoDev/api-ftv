<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posicoes_chaveamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chaveamento_id')->constrained('chaveamento_campeonato')->onDelete('cascade');
            $table->string('fase', 50); // oitavas, quartas, semi, final, disputa_terceiro, grupos
            $table->integer('posicao'); // número da posição dentro da fase (1, 2, 3, 4...)
            $table->integer('ordem_exibicao')->nullable(); // ordem para exibir na tela
            $table->foreignId('partida_id')->nullable()->constrained('partidas_campeonato')->onDelete('set null');
            
            // De onde vêm os competidores (seed inicial ou vencedor de outra posição)
            $table->foreignId('origem_inscricao1_posicao_id')->nullable()->constrained('posicoes_chaveamento')->onDelete('set null');
            $table->foreignId('origem_inscricao2_posicao_id')->nullable()->constrained('posicoes_chaveamento')->onDelete('set null');
            
            // Seeds iniciais (para primeira rodada) ou competidores atuais
            $table->foreignId('inscricao1_id')->nullable()->constrained('inscricoes_campeonato')->onDelete('set null');
            $table->foreignId('inscricao2_id')->nullable()->constrained('inscricoes_campeonato')->onDelete('set null');
            $table->integer('seed_inscricao1')->nullable(); // seed/ranking inicial
            $table->integer('seed_inscricao2')->nullable();
            
            // Vencedor que avança
            $table->foreignId('inscricao_vencedora_id')->nullable()->constrained('inscricoes_campeonato')->onDelete('set null');
            $table->foreignId('proxima_posicao_vencedor_id')->nullable()->constrained('posicoes_chaveamento')->onDelete('set null');
            $table->foreignId('proxima_posicao_perdedor_id')->nullable()->constrained('posicoes_chaveamento')->onDelete('set null'); // para chave de perdedores
            
            $table->string('status', 30)->default('aguardando'); // aguardando, pronta, em_andamento, finalizada
            $table->timestamps();

            $table->index(['chaveamento_id', 'fase', 'posicao']);
            $table->index('partida_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posicoes_chaveamento');
    }
};
