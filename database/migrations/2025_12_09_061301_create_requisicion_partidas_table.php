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
        Schema::create('requisiciones_partidas', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('numero_partida')->default(1);
            $table->decimal('cantidad', 14, 3)->default(0);

            $table->decimal('presupuesto_iva', 14, 2)->default(0);

            $table->text('descripcion')->nullable();

            $table->foreignId('unidad_medida_id')
                ->constrained('unidades_medidas')
                ->cascadeOnUpdate();

            $table->foreignId('requisicion_id')
                ->constrained('requisiciones')
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
        Schema::dropIfExists('requisiciones_partidas');
    }
};
