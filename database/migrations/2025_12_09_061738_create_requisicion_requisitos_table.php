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
        Schema::create('requisiciones_requisitos', function (Blueprint $table) {
            $table->id();

            $table->longText('requisitos_tecnicos')->nullable();
            $table->longText('requisitos_economicos')->nullable();
            $table->longText('informacion_adicional')->nullable();

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
        Schema::dropIfExists('requisicion_requisitos');
    }
};
