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
        Schema::create('observaciones_seguimiento', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('seguimiento_id')->index('seguimiento_id');
            $table->integer('usuario_id')->index('usuario_id');
            $table->string('medio_contacto', 20);
            $table->boolean('contacto_fallido')->nullable()->default(false);
            $table->string('motivo_no_contacto')->nullable();
            $table->text('observacion');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('observaciones_seguimiento');
    }
};
