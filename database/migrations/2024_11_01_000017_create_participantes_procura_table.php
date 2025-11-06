<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participantes_procura', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('procura_id')->constrained('procura_parceiros')->onDelete('cascade');
            $table->foreignUuid('usuario_id')->constrained('users')->onDelete('cascade');
            $table->string('status', 30)->default('interessado');
            $table->timestamps();

            $table->unique(['procura_id', 'usuario_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participantes_procura');
    }
};
