<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quadras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arena_id')->constrained('arenas')->onDelete('cascade');
            $table->string('nome', 50);
            $table->string('comprimento')->nullable();
            $table->string('largura')->nullable();
            $table->decimal('valor_hora', 10, 2)->nullable();
            $table->boolean('coberta')->default(false);
            $table->boolean('iluminacao')->default(true);
            $table->boolean('ativa')->default(true);
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index('arena_id');
            $table->index('ativa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quadras');
    }
};
