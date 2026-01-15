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
        Schema::create('procedimiento_etapa_responsables', function (Blueprint $table) {
            $table->id();

            $table->unsignedSmallInteger('orden')->default(0);

            $table->boolean('requiere_firma')->default(false);
            $table->string('tipo_firma', 20)->nullable();

            $table->boolean('activo')->default(true);

            $table->foreignId('tipo_procedimiento_id')->constrained('tipos_procedimientos')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('etapa_id')->constrained('etapas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('cargo_id')->nullable()->constrained('cargos')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedimiento_etapa_responsables');
    }
};
