<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('rangos_procedimientos', function (Blueprint $table) {
            $table->id();

            $table->decimal('limite_inferior', 14, 2)->default(0);
            $table->decimal('limite_superior', 14, 2)->default(0);

            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);

            $table->foreignId('anio_fiscal_id')->constrained('años_fiscales')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('tipo_procedimiento_id')->constrained('tipos_procedimientos')->cascadeOnUpdate()->restrictOnDelete(); //  (AD, I3P, LPF, LPE, LPM)

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rangos_procedimientos');
    }
};
