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
        Schema::table('matriculas', function (Blueprint $table) {
            $table->foreign(['estudiante_id', 'estudiante_id', 'estudiante_id', 'estudiante_id', 'estudiante_id', 'estudiante_id'], '1')->references(['id', 'id', 'id', 'id', 'id', 'id'])->on('estudiantes')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['estudiante_id'], '1')->references(['id'])->on('estudiantes')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['periodo_id', 'periodo_id', 'periodo_id'], '2')->references(['id', 'id', 'id'])->on('periodos')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['periodo_id', 'periodo_id', 'periodo_id'], '2')->references(['id', 'id', 'id'])->on('periodos')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['programa_id', 'programa_id'], '3')->references(['id', 'id'])->on('programas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['programa_id'], '3')->references(['id'])->on('programas')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matriculas', function (Blueprint $table) {
            $table->dropForeign('1');
            $table->dropForeign('1');
            $table->dropForeign('2');
            $table->dropForeign('2');
            $table->dropForeign('3');
            $table->dropForeign('3');
        });
    }
};
