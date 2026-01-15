<?php

namespace Database\Seeders;

use App\Models\Catalogos\Etapa;
use App\Models\Catalogos\TipoDocumento;
use App\Models\Catalogos\TipoProcedimiento;
use App\Models\Configuracion\DocumentoRequerido;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentoRequeridoSeeder extends Seeder {

    public function run(): void {
        $tp = fn(string $c) => TipoProcedimiento::where('clave', $c)->first();
        $et = fn(string $c) => Etapa::where('clave', $c)->first();
        $td = fn(string $c) => TipoDocumento::where('clave', $c)->first();

        $reglas = [
            ['INTEGRACION_EXPEDIENTE', 'REQUISICION',         10, true, 1],
            ['INTEGRACION_EXPEDIENTE', 'ANEXO_TECNICO',       20, true, 1],
            ['INTEGRACION_EXPEDIENTE', 'COTIZACION',          30, false, 1],
            ['AUTORIZACION_REQUISICION', 'OFICIO_AUTORIZACION', 10, true, 1],

            ['PUBLICACION', 'CONVOCATORIA', 10, false, 1],
            ['PUBLICACION', 'VERSION_PUBLICA', 20, false, 1],
        ];

        foreach (['AD', 'I3P', 'LPF', 'LPE', 'LPM'] as $claveTipo) {
            $tipo = $tp($claveTipo);
            if (!$tipo) continue;

            foreach ($reglas as [$claveEtapa, $claveDoc, $orden, $obligatorio, $cantidad]) {
                $etapa = $et($claveEtapa);
                $doc = $td($claveDoc);

                if (!$etapa || !$doc) continue;

                DocumentoRequerido::updateOrCreate(
                    [
                        'tipo_procedimiento_id' => $tipo->id,
                        'etapa_id' => $etapa->id,
                        'tipo_documento_id' => $doc->id,
                    ],
                    [
                        'orden' => $orden,
                        'obligatorio' => $obligatorio,
                        'cantidad' => $cantidad,
                        'activo' => true,
                    ]
                );
            }
        }
    }
}
