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
        Schema::create('personas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('primer_nombre', 50);
            $table->string('segundo_nombre', 50)->nullable();
            $table->string('primer_apellido', 50);
            $table->string('segundo_apellido', 50)->nullable();
            $table->string('documento', 20)->unique('documento');
            $table->string('correo', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->index(['documento'], 'idx_personas_documento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
