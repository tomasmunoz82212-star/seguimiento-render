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
        Schema::create('reportes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('estudiante_id')->index('idx_reportes_estudiante');
            $table->integer('periodo_id')->index('idx_reportes_periodo');
            $table->integer('usuario_id')->index('usuario_id');
            $table->integer('programa_id')->index('programa_id');
            $table->integer('materia_id')->nullable()->index('materia_id');
            $table->enum('tipo', ['academico', 'asistencia', 'comportamiento']);
            $table->text('descripcion');
            $table->enum('estado', ['pendiente', 'en_seguimiento', 'cerrado'])->nullable()->default('pendiente')->index('idx_reportes_estado');
            $table->dateTime('fecha_limite_seguimiento')->nullable();
            $table->enum('nivel_alerta', ['verde', 'naranja', 'rojo', 'expirado'])->nullable()->default('verde')->index('idx_reportes_nivel_alerta');
            $table->timestamp('creado_en')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
