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
        Schema::create('configuracion_sistema', function (Blueprint $table) {
            $table->integer('id')->default(1)->primary();
            $table->integer('dias_limite_seguimiento')->nullable()->default(5);
            $table->integer('dias_alerta_naranja')->nullable()->default(3);
            $table->integer('dias_alerta_roja')->nullable()->default(1);
            $table->boolean('modo_prueba_minutos')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_sistema');
    }
};
