<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chaveamento_campeonato', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias_campeonato')->onDelete('cascade');
            $table->string('tipo_chaveamento', 30); // eliminacao_simples, eliminacao_dupla, grupos_eliminatorias, round_robin
            $table->integer('total_participantes');
            $table->boolean('disputa_terceiro_lugar')->default(true);
            $table->string('criterio_desempate', 50)->nullable(); // saldo_sets, saldo_pontos, confronto_direto
            $table->boolean('chaveamento_gerado')->default(false);
            $table->timestamp('gerado_em')->nullable();
            $table->timestamps();

            $table->unique('categoria_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chaveamento_campeonato');
    }
};
