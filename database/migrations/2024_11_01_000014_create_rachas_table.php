<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rachas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('criador_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('quadra_id')->constrained('quadras')->onDelete('cascade');
            $table->foreignUuid('juiz_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('data_racha');
            $table->integer('duracao_minutos')->default(60);
            $table->decimal('valor_total', 10, 2);
            $table->string('payment_intent_id', 200)->nullable();
            $table->string('pagamento_status', 30)->default('pendente');
            $table->string('status', 30)->default('aguardando_jogadores');
            $table->integer('dupla_vencedora')->nullable();
            $table->integer('placar_dupla1')->nullable();
            $table->integer('placar_dupla2')->nullable();
            $table->string('nivel_sugerido', 30)->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamp('finalizado_at')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('data_racha');
            $table->index('quadra_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rachas');
    }
};
