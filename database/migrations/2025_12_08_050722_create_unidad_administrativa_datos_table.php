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
        Schema::create('unidad_administrativa_datos', function (Blueprint $table) {
            $table->id();

            $table->string('telefono', 20)->nullable();
            $table->string('extension', 10)->nullable();

            $table->foreignId('titular_id')->nullable()->constrained('titulares')->nullOnDelete();
            $table->foreignId('unidad_administrativa_id')->constrained('unidades_administrativas')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidad_administrativa_datos');
    }
};
