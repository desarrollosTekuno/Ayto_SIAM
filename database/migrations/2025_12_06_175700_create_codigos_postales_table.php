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
        Schema::create('codigos_postales', function (Blueprint $table) {
            $table->id();

            $table->string('cp', 5)->index();
            $table->string('asentamiento', 150);
            $table->string('tipo_asentamiento', 50)->nullable();
            $table->string('zona', 20)->nullable();

            $table->foreignId('estado_id')->nullable()->constrained('estados')->nullOnDelete();
            $table->foreignId('municipio_id')->nullable()->constrained('municipios')->nullOnDelete();
            $table->foreignId('colonia_id')->nullable()->constrained('colonias')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codigos_postales');
    }
};
