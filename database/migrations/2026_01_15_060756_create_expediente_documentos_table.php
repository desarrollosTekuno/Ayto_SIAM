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
        Schema::create('expediente_documentos', function (Blueprint $table) {
            $table->id();

            $table->string('folio_documento', 80)->nullable();
            $table->date('fecha_documento')->nullable();

            $table->string('archivo', 255)->nullable();
            $table->boolean('es_version_publica')->default(false);
            $table->unsignedSmallInteger('version')->default(1);
            $table->boolean('activo')->default(true);
            $table->text('observaciones')->nullable();

            $table->foreignId('expediente_id')->constrained('expedientes')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('tipo_documento_id')->constrained('tipos_documentos')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('subido_por')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expediente_documentos');
    }
};
