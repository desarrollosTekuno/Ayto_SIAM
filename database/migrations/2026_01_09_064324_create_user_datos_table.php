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
        Schema::create('user_datos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellido_paterno', 100);
            $table->string('apellido_materno', 100)->nullable();

            $table->foreignId('unidad_administrativa_id');
            $table->foreign('unidad_administrativa_id')
            ->references('id')->on('unidades_administrativas')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();

            $table->foreignId('dependencia_id');
            $table->foreign('dependencia_id')
            ->references('id')->on('dependencias')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();

            $table->foreignId('user_id')
            ->unique()
            ->constrained('users')
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
        Schema::dropIfExists('user_datos');
    }
};
