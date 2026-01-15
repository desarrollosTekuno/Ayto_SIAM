<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('estatus_expedientes', function (Blueprint $table) {
            $table->id();

            $table->string('clave', 40)->unique(); // CAPTURADO, EN_REVISION, OBSERVADO
            $table->string('nombre', 120);

            $table->string('descripcion', 255)->nullable();

            $table->string('color_estatus', 25)->nullable();
            $table->unsignedSmallInteger('orden')->default(0);

            $table->boolean('activo')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['activo', 'orden', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estatus_expedientes');
    }
};
