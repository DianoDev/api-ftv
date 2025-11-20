<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campeonatos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizador_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('arena_id')->nullable()->constrained('arenas')->onDelete('set null');
            $table->string('nome', 200);
            $table->text('descricao')->nullable();
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->string('tipo', 30)->nullable();
            $table->string('tipo_inscricao')->nullable();
            $table->text('regras')->nullable();
            $table->string('status', 30)->default('inscricoes_abertas');
            $table->text('foto_capa')->nullable();
            $table->timestamps();

            $table->index(['status', 'data_inicio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campeonatos');
    }
};
