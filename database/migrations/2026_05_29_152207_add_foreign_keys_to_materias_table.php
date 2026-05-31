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
        Schema::table('materias', function (Blueprint $table) {
            $table->foreign(['programa_id', 'programa_id', 'programa_id', 'programa_id', 'programa_id', 'programa_id'], '1')->references(['id', 'id', 'id', 'id', 'id', 'id'])->on('programas')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['programa_id'], '1')->references(['id'])->on('programas')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materias', function (Blueprint $table) {
            $table->dropForeign('1');
            $table->dropForeign('1');
        });
    }
};
