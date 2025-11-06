<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procura_parceiros', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('criador_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('quadra_id')->nullable()->constrained('quadras')->onDelete('set null');
            $table->date('data_jogo');
            $table->time('hora_jogo');
            $table->string('nivel_desejado', 30)->nullable();
            $table->string('lado_desejado', 30)->nullable();
            $table->integer('vagas');
            $table->string('tipo_busca', 30)->default('parceiro');
            $table->text('descricao')->nullable();
            $table->string('status', 30)->default('aberto');
            $table->timestamps();

            $table->index(['status', 'data_jogo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procura_parceiros');
    }
};
