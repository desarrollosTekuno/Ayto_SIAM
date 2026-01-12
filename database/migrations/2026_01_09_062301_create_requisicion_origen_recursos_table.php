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
        Schema::create('requisiciones_origenes_recursos', function (Blueprint $table) {
            $table->id();

            $table->string('origen', 150);

            $table->foreignId('requisicion_id');
            $table->foreign('requisicion_id')
                ->references('id')->on('requisiciones')
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
        Schema::dropIfExists('requisiciones_origenes_recursos');
    }
};
