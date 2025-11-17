<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pontos_partida_campeonato', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partida_id')->constrained('partidas_campeonato')->onDelete('cascade');
            $table->foreignId('set_id')->constrained('sets_partida_campeonato')->onDelete('cascade');
            $table->foreignId('inscricao_pontuadora_id')->constrained('inscricoes_campeonato')->onDelete('cascade');
            $table->foreignId('jogador_autor_id')->constrained('users')->onDelete('cascade');
            $table->string('tipo_ponto', 50); // ace, ataque, bloqueio, erro_adversario, erro_saque, erro_ataque, erro_recepcao
            $table->integer('placar_inscricao1'); // placar no momento do ponto
            $table->integer('placar_inscricao2');
            $table->integer('sequencia'); // ordem do ponto no set
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->index(['partida_id', 'set_id']);
            $table->index('inscricao_pontuadora_id');
            $table->index('jogador_autor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pontos_partida_campeonato');
    }
};
