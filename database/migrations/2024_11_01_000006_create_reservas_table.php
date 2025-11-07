<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quadra_id')->constrained('quadras')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->date('data_reserva');
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->decimal('valor', 10, 2);
            $table->string('status', 20)->default('pendente');
            $table->string('metodo_pagamento', 50)->nullable();
            $table->boolean('pagamento_confirmado')->default(false);
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index(['quadra_id', 'data_reserva']);
            $table->index('usuario_id');
            $table->index('status');
            $table->unique(['quadra_id', 'data_reserva', 'hora_inicio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
