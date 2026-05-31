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
        Schema::table('usuarios', function (Blueprint $table) {
            $table->foreign(['persona_id', 'persona_id', 'persona_id', 'persona_id', 'persona_id', 'persona_id'], '1')->references(['id', 'id', 'id', 'id', 'id', 'id'])->on('personas')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['persona_id'], '1')->references(['id'])->on('personas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['rol_id', 'rol_id', 'rol_id'], '2')->references(['id', 'id', 'id'])->on('roles')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['rol_id', 'rol_id', 'rol_id'], '2')->references(['id', 'id', 'id'])->on('roles')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign('1');
            $table->dropForeign('1');
            $table->dropForeign('2');
            $table->dropForeign('2');
        });
    }
};
