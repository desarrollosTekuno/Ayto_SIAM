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
        Schema::create('requisiciones_estatus', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 30)->unique();     // CAPTURADA, ENVIADA, OBSERVADA, AUTORIZADA, RECHAZADA, TURNADA
            $table->string('nombre', 100);
            $table->string('descripcion', 255)->nullable();

            $table->boolean('es_final')->default(false);
            $table->boolean('activo')->default(true);
            $table->smallInteger('orden')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisicion_estatuses');
    }
};
