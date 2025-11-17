<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estatisticas_jogador_racha', function (Blueprint $table) {
            $table->id();
            $table->foreignId('racha_id')->constrained('rachas')->onDelete('cascade');
            $table->foreignId('jogador_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('dupla_id')->constrained('duplas_racha')->onDelete('cascade');
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

            $table->unique(['racha_id', 'jogador_id']);
            $table->index('dupla_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estatisticas_jogador_racha');
    }
};
