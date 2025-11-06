<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avaliacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('avaliador_id')->constrained('users')->onDelete('cascade');
            $table->string('tipo', 30);
            $table->uuid('referencia_id');
            $table->integer('nota');
            $table->text('comentario')->nullable();
            $table->timestamps();

            $table->unique(['avaliador_id', 'tipo', 'referencia_id']);
        });

        // Adicionar constraint para nota
    }

    public function down(): void
    {
        Schema::dropIfExists('avaliacoes');
    }
};
