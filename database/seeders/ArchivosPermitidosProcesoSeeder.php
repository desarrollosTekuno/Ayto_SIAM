<?php

namespace Database\Seeders;

use App\Models\Catalogos\TipoProcedimiento;
use App\Models\Configuracion\ArchivosPermitidosProceso;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArchivosPermitidosProcesoSeeder extends Seeder {

    public function run(): void {
        $extensiones = [
            'PDF',
            'DOC', 'DOCX',
            'XLS', 'XLSX',
            'CSV',
            'XML',
            'PNG', 'JPG', 'JPEG', 'GIF',
            'MP4',
        ];

        $tiposProcesos = TipoProcedimiento::where('activo', true)->get();

        if ($tiposProcesos->isEmpty()) {
            return;
        }

        foreach ($tiposProcesos as $tipo) {
            foreach ($extensiones as $ext) {
                ArchivosPermitidosProceso::firstOrCreate(
                    [
                        'tipo_proceso_id' => $tipo->id,
                        'extension' => $ext,
                    ],
                    [
                        'peso_max_mb' => 8,
                        'obligatorio' => false,
                        'activo' => true,
                    ]
                );
            }
        }
    }
}
