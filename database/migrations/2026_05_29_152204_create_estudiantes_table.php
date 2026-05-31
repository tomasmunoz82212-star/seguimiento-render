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
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('documento', 20)->unique('documento');
            $table->string('nombre', 100)->index('idx_estudiantes_nombre');
            $table->string('correo', 100)->nullable();
            $table->string('telefono', 20)->nullable();

            $table->index(['documento'], 'idx_estudiantes_documento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
