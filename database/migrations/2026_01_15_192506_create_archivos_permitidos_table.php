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
        Schema::create('archivos_permitidos', function (Blueprint $table) {
            $table->id();
            $table->string('extension', 10);

            $table->boolean('obligatorio')->default(false);
            $table->boolean('activo')->default(true);

            $table->foreignId('tipo_procedimiento_id')->nullable()->constrained('tipos_procedimientos')->cascadeOnUpdate();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archivos_permitidos');
    }
};
