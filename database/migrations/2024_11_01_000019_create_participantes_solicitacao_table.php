<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participantes_solicitacao', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('solicitacao_id')->constrained('solicitacoes_racha')->onDelete('cascade');
            $table->foreignUuid('usuario_id')->constrained('users')->onDelete('cascade');
            $table->string('status', 30)->default('interessado');
            $table->decimal('valor_pago', 10, 2)->default(0);
            $table->boolean('pagamento_confirmado')->default(false);
            $table->string('metodo_pagamento', 50)->nullable();
            $table->text('comprovante_url')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamp('confirmado_em')->nullable();
            $table->timestamps();

            $table->index(['solicitacao_id', 'status']);
            $table->unique(['solicitacao_id', 'usuario_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participantes_solicitacao');
    }
};
