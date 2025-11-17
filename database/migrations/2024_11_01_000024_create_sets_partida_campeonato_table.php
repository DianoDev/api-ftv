<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sets_partida_campeonato', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partida_id')->constrained('partidas_campeonato')->onDelete('cascade');
            $table->integer('numero_set'); // 1, 2, 3...
            $table->integer('pontos_inscricao1')->default(0);
            $table->integer('pontos_inscricao2')->default(0);
            $table->foreignId('inscricao_vencedora_id')->nullable()->constrained('inscricoes_campeonato')->onDelete('set null');
            $table->string('status', 30)->default('em_andamento'); // em_andamento, finalizado
            $table->timestamp('iniciado_em')->nullable();
            $table->timestamp('finalizado_em')->nullable();
            $table->timestamps();

            $table->unique(['partida_id', 'numero_set']);
            $table->index('inscricao_vencedora_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sets_partida_campeonato');
    }
};
