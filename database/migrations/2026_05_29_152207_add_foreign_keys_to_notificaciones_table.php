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
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->foreign(['usuario_id', 'usuario_id', 'usuario_id', 'usuario_id', 'usuario_id', 'usuario_id'], '1')->references(['id', 'id', 'id', 'id', 'id', 'id'])->on('usuarios')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['usuario_id'], '1')->references(['id'])->on('usuarios')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['reporte_id', 'reporte_id', 'reporte_id'], '2')->references(['id', 'id', 'id'])->on('reportes')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['reporte_id', 'reporte_id', 'reporte_id'], '2')->references(['id', 'id', 'id'])->on('reportes')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['periodo_id', 'periodo_id'], '3')->references(['id', 'id'])->on('periodos')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['periodo_id'], '3')->references(['id'])->on('periodos')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->dropForeign('1');
            $table->dropForeign('1');
            $table->dropForeign('2');
            $table->dropForeign('2');
            $table->dropForeign('3');
            $table->dropForeign('3');
        });
    }
};
