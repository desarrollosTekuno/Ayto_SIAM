<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('tipos_documentos', function (Blueprint $table) {
            $table->id();

            $table->string('clave', 40)->unique();
            $table->string('nombre', 150);
            $table->string('descripcion', 255)->nullable();
            $table->boolean('requiere_version_publica')->default(false);
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
        Schema::dropIfExists('tipo_documentos');
    }
};
