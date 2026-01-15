<?php

namespace Database\Seeders;

use App\Models\Catalogos\Etapa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EtapaSeeder extends Seeder {
    public function run(): void {
        $items = [
            // ===================== ETAPA 1 =====================
            ['clave' => 'CAPTURA_REQUISICION', 'nombre' => 'Captura de requisición', 'orden_default' => 10],
            ['clave' => 'REVISION_REQUISICION', 'nombre' => 'Revisión de requisición', 'orden_default' => 20],
            ['clave' => 'AUTORIZACION_REQUISICION', 'nombre' => 'Autorización de requisición', 'orden_default' => 30],

            ['clave' => 'INTEGRACION_EXPEDIENTE', 'nombre' => 'Integración de expediente', 'orden_default' => 40],
            ['clave' => 'REVISION_EXPEDIENTE', 'nombre' => 'Revisión de expediente', 'orden_default' => 50],
            ['clave' => 'SUBSANACION_OBSERVACIONES', 'nombre' => 'Subsanación de observaciones', 'orden_default' => 60],

            ['clave' => 'PUBLICACION', 'nombre' => 'Publicación', 'orden_default' => 70],


            // ['clave' => 'JUNTA_ACLARACIONES', 'nombre' => 'Junta de aclaraciones', 'orden_default' => 110],
            // ['clave' => 'RECEPCION_PROPUESTAS', 'nombre' => 'Recepción de propuestas', 'orden_default' => 120],
            // ['clave' => 'EVALUACION', 'nombre' => 'Evaluación', 'orden_default' => 130],
            // ['clave' => 'DICTAMEN', 'nombre' => 'Dictamen', 'orden_default' => 140],
            // ['clave' => 'FALLO', 'nombre' => 'Fallo', 'orden_default' => 150],
            // ['clave' => 'CIERRE', 'nombre' => 'Cierre', 'orden_default' => 160],
        ];

        foreach ($items as $it) {
            Etapa::updateOrCreate(
                ['clave' => $it['clave']],
                [
                    'nombre' => $it['nombre'],
                    'descripcion' => $it['descripcion'] ?? null,
                    'orden_default' => $it['orden_default'],
                    'activo' => true,
                ]
            );
        }
    }
}
