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
        Schema::create('documentos_vinculos', function (Blueprint $table) {
            $table->id();

            $table->string('vinculable_type');
            $table->unsignedBigInteger('vinculable_id');
            $table->string('tipo', 50)->nullable();
            $table->string('descripcion', 255)->nullable();

            $table->foreignId('documento_id');
            $table->foreign('documento_id')
                ->references('id')->on('documentos')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_vinculos');
    }
};
