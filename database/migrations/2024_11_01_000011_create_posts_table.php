<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('usuario_id')->constrained('users')->onDelete('cascade');
            $table->string('tipo', 30)->nullable();
            $table->text('conteudo')->nullable();
            $table->text('midia_url')->nullable();
            $table->text('thumbnail_url')->nullable();
            $table->json('tags')->nullable();
            $table->integer('curtidas')->default(0);
            $table->integer('visualizacoes')->default(0);
            $table->timestamps();
            
            $table->index(['usuario_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
