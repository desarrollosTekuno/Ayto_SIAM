<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('examples', function (Blueprint $table) {
            $table->id();

            $table->string('nombre_receta', 150);            // MdTextInput
            $table->string('codigo_receta', 40)->unique();   // MdTextInput
            $table->string('chef_autor', 120);               // MdTextInput

            $table->string('correo_contacto', 150)           // MdEmailInput
                ->nullable();
            $table->string('telefono_contacto', 25)          // MdPhoneInput
                ->nullable();

            $table->string('categoria', 50)                  // MdSelect (postre, sopas, carnes…)
                ->nullable();
            $table->unsignedBigInteger('cocina_id')          // MdSelectSearch (mexicana, italiana…)
                ->nullable();

            $table->unsignedTinyInteger('porciones')         // MdNumberInput
                ->default(1);
            $table->unsignedTinyInteger('nivel_dificultad')  // MdNumberInput (1–5)
                ->default(1);
            $table->unsignedSmallInteger('tiempo_preparacion_min') // MdNumberInput
                ->default(0);

            $table->date('fecha_publicacion')                // MdDateInput
                ->nullable();
            $table->time('hora_sugerida_servicio')           // MdTimeInput (hora ideal para servir)
                ->nullable();

            $table->unsignedTinyInteger('nivel_picante')     // MdSlider 0–100
                ->default(0);

            $table->boolean('es_vegetariana')                // MdCheckbox
                ->default(false);
            $table->boolean('es_vegana')                     // MdCheckbox
                ->default(false);
            $table->boolean('requiere_horno')                // MdSwitch
                ->default(false);

            $table->text('descripcion_breve')                // MdTextarea (intro corta)
                ->nullable();
            $table->longText('ingredientes_html')            // MdRichText (lista con estilos)
                ->nullable();
            $table->longText('preparacion_html')             // MdRichText (instrucciones paso a paso)
                ->nullable();
            $table->text('tips_extra')                       // MdTextarea
                ->nullable();

            $table->string('foto_principal_path')            // MdFileInput
                ->nullable();
            $table->text('galeria_imagenes_path')            // MdUploadArea (galería de fotos)
                ->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('examples');
    }
};
