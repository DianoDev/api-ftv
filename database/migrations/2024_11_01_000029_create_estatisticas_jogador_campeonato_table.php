<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estatisticas_jogador_campeonato', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias_campeonato')->onDelete('cascade');
            $table->foreignId('jogador_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('inscricao_id')->constrained('inscricoes_campeonato')->onDelete('cascade');
            $table->integer('partidas_jogadas')->default(0);
            $table->integer('vitorias')->default(0);
            $table->integer('derrotas')->default(0);
            $table->integer('sets_ganhos')->default(0);
            $table->integer('sets_perdidos')->default(0);
            $table->integer('pontos_feitos')->default(0);
            $table->integer('pontos_sofridos')->default(0);
            $table->integer('aces')->default(0);
            $table->integer('ataques_ponto')->default(0);
            $table->integer('bloqueios')->default(0);
            $table->integer('erros_saque')->default(0);
            $table->integer('erros_ataque')->default(0);
            $table->decimal('aproveitamento', 5, 2)->default(0); // percentual
            $table->timestamps();

            $table->unique(['categoria_id', 'jogador_id']);
            $table->index('inscricao_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estatisticas_jogador_campeonato');
    }
};
