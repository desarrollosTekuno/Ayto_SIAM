<?php

namespace Database\Seeders;

use App\Models\Catalogos\Etapa;
use App\Models\Catalogos\TipoProcedimiento;
use App\Models\Configuracion\ProcedimientoEtapa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProcedimientoEtapaSeeder extends Seeder {
    public function run(): void {
        $tp = fn(string $clave) => TipoProcedimiento::where('clave', $clave)->firstOrFail();
        $et = fn(string $clave) => Etapa::where('clave', $clave)->firstOrFail();

        $flujoBase = [
            ['CAPTURA_REQUISICION', 10, true],
            ['REVISION_REQUISICION', 20, true],
            ['AUTORIZACION_REQUISICION', 30, true],
            ['INTEGRACION_EXPEDIENTE', 40, true],
            ['REVISION_EXPEDIENTE', 50, true],
            ['SUBSANACION_OBSERVACIONES', 60, true],
            ['PUBLICACION', 70, true],
        ];

        foreach (['AD', 'I3P', 'LPF', 'LPE', 'LPM'] as $claveTipo) {
            $tipo = $tp($claveTipo);

            foreach ($flujoBase as [$claveEtapa, $orden, $obligatoria]) {
                ProcedimientoEtapa::updateOrCreate(
                    [
                        'tipo_procedimiento_id' => $tipo->id,
                        'etapa_id' => $et($claveEtapa)->id,
                    ],
                    [
                        'orden' => $orden,
                        'obligatoria' => $obligatoria,
                        'permite_retroceso' => true,
                        'activo' => true,
                    ]
                );
            }
        }
    }
}
