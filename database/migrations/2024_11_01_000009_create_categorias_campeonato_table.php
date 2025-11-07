<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias_campeonato', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campeonato_id')->constrained('campeonatos')->onDelete('cascade');
            $table->string('nome', 100);
            $table->string('genero', 30)->nullable();
            $table->string('nivel', 30)->nullable();
            $table->integer('max_duplas')->nullable();
            $table->decimal('valor_inscricao', 10, 2)->default(0);
            $table->json('premiacao')->nullable();
            $table->string('status', 30)->default('inscricoes_abertas');
            $table->timestamps();

            $table->index('campeonato_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_campeonato');
    }
};
