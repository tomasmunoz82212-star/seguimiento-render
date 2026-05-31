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
        Schema::table('observaciones_seguimiento', function (Blueprint $table) {
            $table->foreign(['seguimiento_id', 'seguimiento_id', 'seguimiento_id', 'seguimiento_id', 'seguimiento_id', 'seguimiento_id'], '1')->references(['id', 'id', 'id', 'id', 'id', 'id'])->on('seguimientos_bienestar')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['seguimiento_id'], '1')->references(['id'])->on('seguimientos_bienestar')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['usuario_id', 'usuario_id', 'usuario_id'], '2')->references(['id', 'id', 'id'])->on('usuarios')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['usuario_id', 'usuario_id', 'usuario_id'], '2')->references(['id', 'id', 'id'])->on('usuarios')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('observaciones_seguimiento', function (Blueprint $table) {
            $table->dropForeign('1');
            $table->dropForeign('1');
            $table->dropForeign('2');
            $table->dropForeign('2');
        });
    }
};
