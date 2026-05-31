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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('usuario_id')->index('idx_notificaciones_usuario');
            $table->integer('reporte_id')->nullable()->index('reporte_id');
            $table->integer('periodo_id')->nullable()->index('idx_notificaciones_periodo');
            $table->string('tipo', 50)->comment('nuevo_reporte, cambio_nivel, caso_cerrado');
            $table->text('mensaje');
            $table->boolean('leida')->nullable()->default(false)->index('idx_notificaciones_leida');
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
