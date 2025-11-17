<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sets_racha', function (Blueprint $table) {
            $table->id();
            $table->foreignId('racha_id')->constrained('rachas')->onDelete('cascade');
            $table->integer('numero_set'); // 1, 2, 3...
            $table->integer('pontos_dupla1')->default(0);
            $table->integer('pontos_dupla2')->default(0);
            $table->integer('dupla_vencedora')->nullable(); // 1 ou 2
            $table->string('status', 30)->default('em_andamento'); // em_andamento, finalizado
            $table->timestamp('iniciado_em')->nullable();
            $table->timestamp('finalizado_em')->nullable();
            $table->timestamps();

            $table->unique(['racha_id', 'numero_set']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sets_racha');
    }
};
