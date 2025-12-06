<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('examples', function (Blueprint $table) {
            $table->id();

            // TEXT INPUT
            $table->string('hechizo', 80);                   // Nombre del hechizo
            $table->string('ingrediente_principal', 100);    // Ingrediente clave del conjuro
            $table->string('codigo_runico', 12)->unique();   // Código mágico: ABC123XYZ

            // EMAIL
            $table->string('correo_mago', 150);              // Correo del mago responsable

            // PHONE
            $table->string('telefono_mago', 20)->nullable(); // Contacto por cristal-comunicador

            // NUMBER
            $table->unsignedTinyInteger('nivel_hechizo')     // Complejidad del hechizo
                ->default(1);                              // 1–9
            $table->decimal('costo_mana', 8, 2)              // Energía requerida
                ->default(0);

            // PASSWORD
            $table->string('password_grimorio', 100)         // Llave del grimorio
                ->nullable();

            // DATES
            $table->date('fecha_ritual')->nullable();        // Día del ritual principal

            // SELECTS
            $table->unsignedBigInteger('bestia_favorita_id') // Bestia asociada al hechizo
                ->nullable();
            $table->string('rango_mago', 30)                 // Aprendiz / Hechicero / Archimago
                ->nullable();

            // BOOLEANOS & TOGGLES
            $table->boolean('acepta_riesgos_magicos')        // Firma de riesgo
                ->default(false);
            $table->boolean('modo_silencioso')               // Sin efectos sonoros molestos
                ->default(false);
            $table->boolean('turno_nocturno')                // Operación nocturna
                ->default(false);
            $table->string('canal_hechizo', 20)              // etereo/fisico
                ->default('etereo');
            $table->string('modo_trabajo', 20)               // normal/estricto
                ->default('normal');

            // TEXTAREA
            $table->text('diario_mago')->nullable();         // Notas del mago

            // FILES
            $table->string('pergaminos_path')->nullable();   // Un archivo principal
            $table->text('documentos_arcanos_path')          // Texto simple con rutas separadas
                ->nullable();

            // RICH TEXT
            $table->longText('grimorio_html')->nullable();   // Entrada del grimorio con formato

            // TIME INPUT
            $table->time('hora_ritual')->nullable();         // Hora exacta del ritual

            // SLIDER
            $table->unsignedTinyInteger('poder_encantamiento') // Intensidad configurada
                ->default(50);                               // 0–100

            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examples');
    }
};
