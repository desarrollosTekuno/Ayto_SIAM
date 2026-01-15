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
        Schema::create('unidades_administrativas', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 150);
            $table->string('siglas', 50)->nullable();
            $table->string('abreviatura', 100)->nullable();
            $table->string('alias', 100)->nullable();
            $table->tinyInteger('tipo')->default(0); // DIRECCION, DEPARTAMENTO, JEFATURA

            $table->foreignId('dependencia_id')->constrained('dependencias')->cascadeOnDelete();
            $table->foreignId('titular_id')->nullable()->constrained('titulares')->nullOnDelete();
            $table->foreignId('unidad_padre_id')->nullable()->constrained('unidades_administrativas')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidades_administrativas');
    }
};
