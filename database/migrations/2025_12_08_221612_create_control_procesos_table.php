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
        Schema::create('control_procesos', function (Blueprint $table) {
            $table->id();

            $table->string('folio', 60)->unique();
            $table->string('estatus', 30);

            $table->unsignedInteger('paso_actual')->default(1);
            $table->unsignedInteger('total_pasos')->default(1);

            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();

            $table->boolean('activo')->default(true);

            $table->foreignId('tipo_proceso_id')
                ->constrained('tipos_procesos')
                ->cascadeOnUpdate();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('control_procesos');
    }
};
