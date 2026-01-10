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
        Schema::create('requisiciones_suficiencias', function (Blueprint $table) {
            $table->id();

            $table->string('tipo_solicitud', 80)->nullable();

            $table->string('oficio_solicitud_numero', 80)->nullable();
            $table->date('oficio_solicitud_fecha_emision')->nullable();
            $table->date('oficio_solicitud_fecha_recepcion_tesoreria')->nullable();

            $table->string('oficio_respuesta_numero', 80)->nullable();
            $table->date('oficio_respuesta_fecha_emision')->nullable();
            $table->date('oficio_respuesta_fecha_recepcion_dependencia')->nullable();

            $table->string('solped_numero', 80)->nullable();
            $table->decimal('monto_autorizado', 14, 2)->default(0);

            $table->foreignId('requisicion_id');

            $table->foreign('requisicion_id')
                ->references('id')->on('requisiciones')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisicion_suficiencias');
    }
};
