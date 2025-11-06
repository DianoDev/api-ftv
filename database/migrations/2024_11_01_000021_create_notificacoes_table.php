<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('usuario_id')->constrained('users')->onDelete('cascade');
            $table->string('tipo', 50);
            $table->string('titulo', 200);
            $table->text('mensagem')->nullable();
            $table->json('dados')->nullable();
            $table->boolean('lida')->default(false);
            $table->timestamps();

            $table->index(['usuario_id', 'lida']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacoes');
    }
};
