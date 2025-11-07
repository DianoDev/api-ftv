<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jogadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nivel', 20)->default('iniciante');
            $table->string('lado_preferido', 20)->default('ambos');
            $table->integer('ranking')->default(1000);
            $table->integer('total_rachas')->default(0);
            $table->integer('vitorias')->default(0);
            $table->integer('derrotas')->default(0);
            $table->string('posicao_preferida', 50)->nullable();
            $table->string('nivel_jogo', 20)->nullable();
            $table->timestamps();

            $table->index('ranking');
            $table->index('nivel');
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jogadores');
    }
};
