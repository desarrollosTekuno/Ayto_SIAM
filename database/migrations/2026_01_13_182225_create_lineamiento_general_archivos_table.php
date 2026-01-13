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
        Schema::create('lineamiento_general_archivos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->index();
            $table->string('titulo', 850)->nullable();

            $table->string('archivo')->nullable();

            $table->foreignId('actualizado_por')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lineamiento_general_archivos');
    }
};
