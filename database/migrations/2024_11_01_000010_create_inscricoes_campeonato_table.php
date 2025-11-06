<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscricoes_campeonato', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('categoria_id')->constrained('categorias_campeonato')->onDelete('cascade');
            $table->foreignUuid('jogador1_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('jogador2_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignUuid('jogador3_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignUuid('jogador4_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('nome_equipe', 100)->nullable();
            $table->string('status', 30)->default('pendente');
            $table->boolean('pagamento_confirmado')->default(false);
            $table->timestamps();

            $table->unique(['categoria_id', 'jogador1_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscricoes_campeonato');
    }
};
