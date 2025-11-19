<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->text('conteudo');
            $table->string('imagem')->nullable();
            $table->timestamp('expira_em');
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index('usuario_id');
            $table->index('expira_em');
            $table->index(['ativo', 'expira_em']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
