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
        Schema::create('seguimientos_bienestar', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('reporte_id')->index('reporte_id');
            $table->integer('usuario_id')->index('usuario_id');
            $table->boolean('dificultad_economica')->nullable()->default(false);
            $table->boolean('trabaja_y_estudia')->nullable()->default(false);
            $table->boolean('falta_apoyo_familiar')->nullable()->default(false);
            $table->boolean('ansiedad_estres')->nullable()->default(false);
            $table->boolean('depresion_tristeza')->nullable()->default(false);
            $table->boolean('baja_autoestima')->nullable()->default(false);
            $table->boolean('desmotivacion')->nullable()->default(false);
            $table->boolean('problema_salud_fisica')->nullable()->default(false);
            $table->boolean('problema_salud_mental')->nullable()->default(false);
            $table->boolean('conflicto_pares')->nullable()->default(false);
            $table->boolean('conflicto_docentes')->nullable()->default(false);
            $table->boolean('bullying_acoso')->nullable()->default(false);
            $table->boolean('dificultad_aprendizaje')->nullable()->default(false);
            $table->boolean('problema_adaptacion')->nullable()->default(false);
            $table->boolean('falta_habitos_estudio')->nullable()->default(false);
            $table->boolean('problema_familiar')->nullable()->default(false);
            $table->boolean('responsabilidad_hogar')->nullable()->default(false);
            $table->boolean('otro')->nullable()->default(false);
            $table->text('detalle_otro')->nullable();
            $table->text('razon_cierre')->nullable();
            $table->enum('estado', ['en_proceso', 'cerrado'])->nullable()->default('en_proceso');
            $table->timestamp('creado_en')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguimientos_bienestar');
    }
};
