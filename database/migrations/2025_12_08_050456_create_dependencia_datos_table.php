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
        Schema::create('dependencia_datos', function (Blueprint $table) {
            $table->id();

            $table->string('telefono', 20);
            $table->string('extension', 10)->nullable();

            $table->foreignId('titular_id')->nullable()->constrained('titulares')->nullOnDelete();

            $table->foreignId('dependencia_id')->constrained('dependencias')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dependencia_datos');
    }
};
