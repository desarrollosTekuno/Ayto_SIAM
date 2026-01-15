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
        Schema::create('titulares', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 150);
            $table->string('apellido_paterno', 150)->nullable();
            $table->string('apellido_materno', 150)->nullable();
            $table->string('correo', 95)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('extension', 10)->nullable();

            $table->foreignId('cargo_id')->nullable()->constrained('cargos')->nullOnDelete();

            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titulares');
    }
};
