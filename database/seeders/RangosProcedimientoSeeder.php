<?php

namespace Database\Seeders;

use App\Models\Configuracion\RangosProcedimiento;
use App\Models\Configuracion\TiposProceso;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RangosProcedimientoSeeder extends Seeder {

    public function run(): void {
        $tipoReq = TiposProceso::where('clave', 'REQ')->first();

        $rangos = [
            [
                'nombre' => 'INVITACION_3_SECATI',
                'limite_inferior' => 40000.00,
                'limite_superior' => 250000.00,
                'orden' => 1,
            ],
            [
                'nombre' => 'INVITACION_3_CMA',
                'limite_inferior' => 260000.00,
                'limite_superior' => 1178573.87,
                'orden' => 2,
            ],
            [
                'nombre' => 'CONCURSO_INVITACION',
                'limite_inferior' => 1178573.88,
                'limite_superior' => 2678577.00,
                'orden' => 3,
            ],
            [
                'nombre' => 'LICITACION_PUBLICA',
                'limite_inferior' => 2678577.01,
                'limite_superior' => 999999999.99,
                'orden' => 4,
            ],
        ];

        foreach ($rangos as $rango) {
            RangosProcedimiento::updateOrCreate(
                [
                    'tipo_proceso_id' => $tipoReq->id,
                    'nombre' => $rango['nombre'],
                ],
                [
                    'limite_inferior' => $rango['limite_inferior'],
                    'limite_superior' => $rango['limite_superior'],
                    'orden' => $rango['orden'],
                    'activo' => true,
                ]
            );
        }
    }
}
