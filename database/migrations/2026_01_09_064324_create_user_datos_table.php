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

            $table->string('cargo', 150)->nullable(); // o cargo_id FK a cargos
            $table->string('telefono', 20)->nullable();
            $table->string('extension', 10)->nullable();

            $table->boolean('activo')->default(true);

            $table->foreignId('unidad_administrativa_id')
                ->nullable()
                ->constrained('unidades_administrativas')
                ->nullOnDelete();

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
