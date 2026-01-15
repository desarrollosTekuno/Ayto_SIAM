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
        Schema::create('procedimiento_etapas', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('orden')->default(0);

            $table->boolean('obligatoria')->default(true);
            $table->boolean('permite_retroceso')->default(true);

            $table->boolean('activo')->default(true);

            $table->foreignId('tipo_procedimiento_id')->constrained('tipos_procedimientos')->cascadeOnUpdate()->restrictOnDelete();

            $table->foreignId('etapa_id')->constrained('etapas')->cascadeOnUpdate()->restrictOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedimiento_etapas');
    }
};
