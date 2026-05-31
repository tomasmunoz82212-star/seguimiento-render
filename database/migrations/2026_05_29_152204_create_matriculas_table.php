<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('matriculas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('estudiante_id')->index('idx_matriculas_estudiante');
            $table->integer('periodo_id')->index('idx_matriculas_periodo');
            $table->integer('programa_id')->index('programa_id');
            $table->tinyInteger('semestre');

            $table->index(['estudiante_id', 'periodo_id'], 'idx_matriculas_estudiante_periodo');
            $table->unique(['estudiante_id', 'periodo_id'], 'unica_matricula');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matriculas');
    }
};
