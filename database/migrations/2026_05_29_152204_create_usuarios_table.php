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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('persona_id')->index('persona_id');
            $table->string('usuario', 50)->index('idx_usuarios_usuario');
            $table->string('contraseña');
            $table->integer('rol_id')->index('idx_usuarios_rol');
            $table->enum('estado', ['activo', 'inactivo'])->nullable()->default('activo')->index('idx_usuarios_estado');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->unique(['usuario'], 'usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
