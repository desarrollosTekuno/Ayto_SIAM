<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void  {
        Schema::create('años_fiscales', function (Blueprint $table) {
            $table->id();

            $table->integer('año')->unique();
            $table->boolean('activo')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['activo', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('años_fiscales');
    }
};
