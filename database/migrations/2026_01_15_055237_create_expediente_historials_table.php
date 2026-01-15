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
        Schema::create('expediente_historial', function (Blueprint $table) {
            $table->id();

            $table->string('accion', 40); // CREA, AVANZA, REGRESA, OBSERVA, AUTORIZA, PUBLICA, etc.
            $table->text('comentario')->nullable();

            $table->foreignId('expediente_id')->constrained('expedientes')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('etapa_id')->nullable()->constrained('etapas')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('estatus_id')->nullable()->constrained('estatus_expedientes')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expediente_historials');
    }
};
