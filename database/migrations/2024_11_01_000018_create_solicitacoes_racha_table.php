<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitacoes_racha', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('criador_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('arena_id')->constrained('arenas')->onDelete('cascade');
            $table->date('data_jogo');
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->decimal('duracao_horas', 3, 1)->nullable();
            $table->integer('limite_participantes');
            $table->integer('participantes_atuais')->default(1);
            $table->decimal('valor_estimado', 10, 2)->nullable();
            $table->decimal('valor_por_pessoa', 10, 2)->nullable();
            $table->string('status', 30)->default('aberta');
            $table->foreignUuid('reserva_id')->nullable()->constrained('reservas')->onDelete('set null');
            $table->foreignUuid('quadra_alocada_id')->nullable()->constrained('quadras')->onDelete('set null');
            $table->string('metodo_pagamento', 50)->nullable();
            $table->string('payment_intent_id', 200)->nullable();
            $table->string('nivel_sugerido', 30)->nullable();
            $table->text('descricao')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamp('data_limite_confirmacao')->nullable();
            $table->timestamps();

            $table->index(['status', 'data_jogo']);
            $table->index(['arena_id', 'data_jogo', 'hora_inicio']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('solicitacoes_racha');
    }
};
