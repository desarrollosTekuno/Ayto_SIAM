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
        Schema::create('direcciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('cveSec', 5)->nullable();
            $table->string('cveURes', 4)->nullable();
            $table->string('abreviatura', 100)->nullable();
            $table->string('usado_en', 20)->default('SIAM')->nullable();

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
        Schema::dropIfExists('direccions');
    }
};
