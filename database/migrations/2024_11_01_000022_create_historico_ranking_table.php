<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historico_ranking', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('usuario_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('racha_id')->nullable()->constrained('rachas')->onDelete('set null');
            $table->integer('ranking_anterior');
            $table->integer('ranking_novo');
            $table->integer('diferenca');
            $table->string('motivo', 50)->nullable();
            $table->timestamps();

            $table->index(['usuario_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historico_ranking');
    }
};
