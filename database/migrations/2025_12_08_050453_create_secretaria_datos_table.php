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
        Schema::create('secretaria_datos', function (Blueprint $table) {
            $table->id();

            $table->string('telefono', 20);
            $table->string('extension', 10)->nullable();

            $table->unsignedBigInteger('titular_id')->nullable();
            $table->foreign('titular_id')->references("id")->on("titulares");

            $table->unsignedBigInteger('secretaria_id')->nullable();
            $table->foreign('secretaria_id')->references("id")->on("secretarias");

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secretaria_datos');
    }
};
