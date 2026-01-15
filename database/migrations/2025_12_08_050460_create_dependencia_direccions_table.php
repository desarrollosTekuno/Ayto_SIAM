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
        Schema::create('dependencia_direcciones', function (Blueprint $table) {
            $table->id();

            $table->string('calle', 150);
            $table->string('numero_exterior', 20);
            $table->string('numero_interior', 20)->nullable();
            $table->string('colonia', 120);
            $table->string('codigo_postal', 10);

            $table->foreignId('estado_id')->nullable()->constrained('estados')->nullOnDelete();

            $table->foreignId('municipio_id')->nullable()->constrained('municipios')->nullOnDelete();

            $table->foreignId('dependencia_id')->constrained('dependencias')->cascadeOnDelete();

            $table->unique('dependencia_id');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dependencia_direccions');
    }
};
