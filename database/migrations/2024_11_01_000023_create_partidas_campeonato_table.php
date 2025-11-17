<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partidas_campeonato', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias_campeonato')->onDelete('cascade');
            $table->foreignId('inscricao1_id')->constrained('inscricoes_campeonato')->onDelete('cascade');
            $table->foreignId('inscricao2_id')->constrained('inscricoes_campeonato')->onDelete('cascade');
            $table->foreignId('quadra_id')->nullable()->constrained('quadras')->onDelete('set null');
            $table->string('fase', 50); // oitavas, quartas, semi, final, disputa_terceiro, fase_grupos
            $table->foreignId('grupo_id')->nullable()->constrained('grupos_campeonato')->onDelete('set null');
            $table->foreignId('posicao_chaveamento_id')->nullable()->constrained('posicoes_chaveamento')->onDelete('set null');
            $table->integer('rodada')->nullable(); // para fase de grupos
            $table->integer('ordem_grupo')->nullable(); // ordem dentro do grupo
            $table->dateTime('data_hora')->nullable();
            $table->integer('duracao_minutos')->nullable();
            $table->foreignId('inscricao_vencedora_id')->nullable()->constrained('inscricoes_campeonato')->onDelete('set null');
            $table->integer('sets_inscricao1')->default(0);
            $table->integer('sets_inscricao2')->default(0);
            $table->string('status', 30)->default('agendada'); // agendada, em_andamento, finalizada, wo
            $table->foreignId('arbitro_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('observacoes')->nullable();
            $table->timestamp('iniciada_em')->nullable();
            $table->timestamp('finalizada_em')->nullable();
            $table->timestamps();

            $table->index(['categoria_id', 'fase']);
            $table->index(['grupo_id', 'rodada']);
            $table->index('posicao_chaveamento_id');
            $table->index(['status', 'data_hora']);
            $table->index('inscricao_vencedora_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partidas_campeonato');
    }
};
