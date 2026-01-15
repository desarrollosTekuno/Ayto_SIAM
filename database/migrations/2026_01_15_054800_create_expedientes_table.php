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
 Schema::create('expedientes', function (Blueprint $table) {
            $table->id();

            $table->string('folio', 35)->unique();
            $table->date('fecha_inicio');
            $table->date('fecha_cierre')->nullable();

            $table->boolean('activo')->default(true);
            $table->foreignId('anio_fiscal_id')->constrained('años_fiscales')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('tipo_procedimiento_id')->constrained('tipos_procedimientos')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('dependencia_id')->constrained('dependencias')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('unidad_administrativa_id')->constrained('unidades_administrativas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('estatus_id')->constrained('estatus_expedientes')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('etapa_actual_id')->constrained('etapas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expedientes');
    }
};
