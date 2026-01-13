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
            $table->string('telefono', 20)->nullable();
            $table->string('extension', 10)->nullable();
            $table->string('celular', 20)->nullable();

            $table->unsignedBigInteger('cargo_id')->nullable();
            $table->foreign('cargo_id')->references("id")->on("cargos");

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
