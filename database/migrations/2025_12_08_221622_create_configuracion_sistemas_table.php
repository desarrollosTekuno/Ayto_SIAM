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
        Schema::create('configuraciones_sistemas', function (Blueprint $table) {
            $table->id();

            $table->string('clave', 80)->unique(); // SESION_MINUTOS_TIMEOUT
            $table->string('tipo', 20)->nullable();            // string|int|decimal|bool|date|time|datetime|list
            $table->string('descripcion', 255)->nullable();
            $table->string('grupo', 50)->nullable(); // sesion, captura, archivos,

            $table->boolean('editable')->default(true);
            $table->boolean('activo')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuraciones_sistemas');
    }
};
