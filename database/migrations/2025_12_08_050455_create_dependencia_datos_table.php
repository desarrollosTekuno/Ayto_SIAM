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

            $table->string('nombre_titular', 150)->nullable();
            $table->string('cargo_titular', 150)->nullable();

            $table->string('telefono', 20);
            $table->string('extension', 10)->nullable();

            $table->unsignedBigInteger('titular_id')->nullable();
            $table->foreign('titular_id')->references("id")->on("titulares");

            $table->unsignedBigInteger('dependencia_id')->nullable();
            $table->foreign('dependencia_id')->references("id")->on("dependencias");

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
