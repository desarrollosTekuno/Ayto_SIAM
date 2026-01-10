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
        Schema::create('requisiciones', function (Blueprint $table) {
            $table->id();

            $table->string('folio', 60)->unique();
            $table->string('alias', 120)->nullable();

            $table->text('descripcion_general')->nullable();

            $table->boolean('posible_adjudicacion_directa')->default(false);
            $table->boolean('autoriza_economias')->default(false);
            $table->boolean('contrato_abierto')->default(false);

            $table->unsignedInteger('dias_para_preguntas')->default(0);
            $table->unsignedInteger('dias_para_dictamen')->default(0);

            $table->string('representante_nombre', 150)->nullable();
            $table->string('representante_cargo', 150)->nullable();

            $table->string('asesor_tecnico_nombre', 150)->nullable();
            $table->string('asesor_tecnico_cargo', 150)->nullable();

            $table->string('area_solicitante_nombre', 150)->nullable();
            $table->string('area_solicitante_cargo', 150)->nullable();

            $table->decimal('presupuesto_total_iva', 14, 2)->default(0);
            $table->date('fecha_entrega_periodo_servicio')->nullable();
            $table->date('fecha_cotizacion_base')->nullable();

            $table->string('lugar_entrega', 255)->nullable();
            $table->string('responsable_entrega', 150)->nullable();
            $table->string('periodo_garantia', 100)->nullable();

            $table->string('horario_atencion_telefono', 100)->nullable();
            $table->string('correo_contacto', 150)->nullable();

            $table->text('observaciones_dependencia')->nullable();
            $table->string('estatus', 30)->default('CAPTURA');
            $table->timestamp('enviado_revision_at')->nullable();

            $table->foreignId('dependencia_id');
            $table->foreign('dependencia_id')
                ->references('id')->on('dependencias')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('unidad_administrativa_id');
            $table->foreign('unidad_administrativa_id')
                ->references('id')->on('unidades_administrativas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('paaas_id')->nullable();
            $table->foreign('paaas_id')
                ->references('id')->on('paaas')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('creado_por_id')->nullable();
            $table->foreign('creado_por_id')
                ->references('id')->on('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('actualizado_por_id')->nullable();
            $table->foreign('actualizado_por_id')
                ->references('id')->on('users')
                ->cascadeOnUpdate()
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
        Schema::dropIfExists('requisicions');
    }
};
