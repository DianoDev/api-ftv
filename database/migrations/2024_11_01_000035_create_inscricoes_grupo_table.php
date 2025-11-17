<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscricoes_grupo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained('grupos_campeonato')->onDelete('cascade');
            $table->foreignId('inscricao_id')->constrained('inscricoes_campeonato')->onDelete('cascade');
            $table->integer('pontos')->default(0);
            $table->integer('jogos')->default(0);
            $table->integer('vitorias')->default(0);
            $table->integer('derrotas')->default(0);
            $table->integer('sets_ganhos')->default(0);
            $table->integer('sets_perdidos')->default(0);
            $table->integer('saldo_sets')->default(0);
            $table->integer('pontos_feitos')->default(0);
            $table->integer('pontos_sofridos')->default(0);
            $table->integer('saldo_pontos')->default(0);
            $table->integer('posicao_grupo')->nullable();
            $table->boolean('classificado')->default(false);
            $table->timestamps();

            $table->unique(['grupo_id', 'inscricao_id']);
            $table->index(['grupo_id', 'pontos', 'saldo_sets']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscricoes_grupo');
    }
};
