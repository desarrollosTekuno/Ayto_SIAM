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
        Schema::create('secretarias', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 150);
            $table->string('cveDep', 5)->nullable();
            $table->string('cveURes', 4)->nullable();
            $table->string('abreviatura', 100)->nullable();
            $table->tinyInteger('tipo')->default(0);
            $table->string('usado_en', 20)->default('SIAM')->nullable();

            $table->foreignId('secretaria_padre_id')
                ->nullable()
                ->constrained('secretarias')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secretarias');
    }
};
