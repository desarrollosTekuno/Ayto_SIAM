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
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('abreviatura', 100)->nullable();
            $table->string('alias', 100)->nullable();

            $table->string('usado_en', 20)->nullable();
            $table->boolean('ayto_biometricos')->default(true)->nullable();

            $table->unsignedBigInteger('area_id')->nullable();
            $table->foreign('area_id')->references("id")->on("areas");

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
        Schema::dropIfExists('departamentos');
    }
};
