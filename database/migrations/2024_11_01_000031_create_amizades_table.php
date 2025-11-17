<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amizades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('amigo_id')->constrained('users')->onDelete('cascade');
            $table->string('status', 30)->default('pendente'); // pendente, aceito, recusado, bloqueado
            $table->timestamp('aceito_em')->nullable();
            $table->timestamps();

            $table->unique(['usuario_id', 'amigo_id']);
            $table->index(['usuario_id', 'status']);
            $table->index(['amigo_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amizades');
    }
};
