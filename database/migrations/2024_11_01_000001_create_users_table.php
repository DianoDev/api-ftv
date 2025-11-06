<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->enum('tipo_usuario', ['jogador', 'arena', 'professor'])->default('jogador');
            $table->string('telefone', 20)->nullable();
            $table->text('foto_url')->nullable();
            $table->string('cidade', 100)->nullable();
            $table->string('estado', 2)->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('genero', 20)->nullable();
            $table->text('bio')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_avaliacoes')->default(0);
            $table->boolean('perfil_completo')->default(false);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('tipo_usuario');
            $table->index(['cidade', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
