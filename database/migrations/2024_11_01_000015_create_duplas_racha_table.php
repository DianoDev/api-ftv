<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('duplas_racha', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('racha_id')->constrained('rachas')->onDelete('cascade');
            $table->integer('numero_dupla');
            $table->foreignUuid('jogador1_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('jogador2_id')->constrained('users')->onDelete('cascade');
            $table->integer('pontos_ranking_ganhos')->default(0);
            $table->timestamps();

            $table->index(['jogador1_id', 'jogador2_id']);
            $table->unique(['racha_id', 'numero_dupla']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duplas_racha');
    }
};
