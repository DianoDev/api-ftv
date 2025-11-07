<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->json('certificacoes')->nullable();
            $table->integer('experiencia_anos')->nullable();
            $table->json('especialidades')->nullable();
            $table->decimal('valor_hora_aula', 10, 2)->nullable();
            $table->text('descricao')->nullable();
            $table->json('disponibilidade')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_avaliacoes')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->unique('user_id');
            $table->index('ativo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professores');
    }
};
