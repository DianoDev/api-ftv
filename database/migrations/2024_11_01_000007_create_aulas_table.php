<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aulas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('professor_id')->constrained('professores')->onDelete('cascade');
            $table->foreignUuid('aluno_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('quadra_id')->nullable()->constrained('quadras')->onDelete('set null');
            $table->date('data_aula');
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->decimal('valor', 10, 2);
            $table->string('tipo', 20)->default('particular');
            $table->string('status', 20)->default('agendada');
            $table->boolean('pagamento_confirmado')->default(false);
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            $table->index('professor_id');
            $table->index('aluno_id');
            $table->index(['data_aula', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aulas');
    }
};
