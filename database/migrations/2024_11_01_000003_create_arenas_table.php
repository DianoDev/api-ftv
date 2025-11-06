<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arenas', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('proprietario_id')->constrained('users')->onDelete('cascade');
            $table->string('nome', 100);
            $table->text('descricao')->nullable();
            $table->string('cnpj', 20)->nullable();
            $table->text('endereco');
            $table->string('cidade', 100);
            $table->string('estado', 2);
            $table->string('cep', 10)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->json('fotos')->nullable();
            $table->json('horario_funcionamento')->nullable();
            $table->json('comodidades')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_avaliacoes')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['cidade', 'estado']);
            $table->index('ativo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arenas');
    }
};
