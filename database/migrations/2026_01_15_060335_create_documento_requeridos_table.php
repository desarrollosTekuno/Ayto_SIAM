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
        Schema::create('documentos_requeridos', function (Blueprint $table) {
            $table->id();

            $table->boolean('obligatorio')->default(true);
            $table->unsignedSmallInteger('cantidad')->default(1);

            $table->unsignedSmallInteger('orden')->default(0);

            $table->boolean('activo')->default(true);

            $table->foreignId('tipo_procedimiento_id')->constrained('tipos_procedimientos')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('etapa_id')->constrained('etapas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('tipo_documento_id')->constrained('tipos_documentos')->cascadeOnUpdate()->restrictOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_requeridos');
    }
};
