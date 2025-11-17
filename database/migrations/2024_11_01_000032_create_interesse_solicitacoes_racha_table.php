<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interesse_solicitacoes_racha', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitacao_id')->constrained('solicitacoes_racha')->onDelete('cascade');
            $table->foreignId('usuario_interessado_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('usuario_criador_id')->constrained('users')->onDelete('cascade');
            $table->string('status', 30)->default('pendente'); // pendente, aceito, recusado
            $table->text('mensagem')->nullable();
            $table->timestamp('respondido_em')->nullable();
            $table->timestamps();

            $table->unique(['solicitacao_id', 'usuario_interessado_id']);
            $table->index(['solicitacao_id', 'status']);
            $table->index(['usuario_interessado_id', 'status']);
            $table->index(['usuario_criador_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interesse_solicitacoes_racha');
    }
};
