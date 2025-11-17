<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pontos_racha', function (Blueprint $table) {
            $table->id();
            $table->foreignId('racha_id')->constrained('rachas')->onDelete('cascade');
            $table->foreignId('set_id')->constrained('sets_racha')->onDelete('cascade');
            $table->integer('dupla_pontuadora'); // 1 ou 2
            $table->foreignId('jogador_autor_id')->constrained('users')->onDelete('cascade');
            $table->string('tipo_ponto', 50); // ace, ataque, bloqueio, erro_adversario, erro_saque, erro_ataque, erro_recepcao
            $table->integer('placar_dupla1'); // placar no momento do ponto
            $table->integer('placar_dupla2');
            $table->integer('sequencia'); // ordem do ponto no set
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->index(['racha_id', 'set_id']);
            $table->index('dupla_pontuadora');
            $table->index('jogador_autor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pontos_racha');
    }
};
