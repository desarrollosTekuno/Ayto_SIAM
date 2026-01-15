<?php

namespace Database\Seeders;

use App\Models\Catalogos\TipoDocumento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoDocumentoSeeder extends Seeder {

public function run(): void {
        $items = [
            ['clave' => 'REQUISICION', 'nombre' => 'Requisición', 'requiere_version_publica' => false],
            ['clave' => 'COTIZACION', 'nombre' => 'Cotización', 'requiere_version_publica' => false],
            ['clave' => 'ANEXO_TECNICO', 'nombre' => 'Anexo técnico', 'requiere_version_publica' => false],
            ['clave' => 'OFICIO_AUTORIZACION', 'nombre' => 'Oficio de autorización', 'requiere_version_publica' => false],

            ['clave' => 'CONVOCATORIA', 'nombre' => 'Convocatoria', 'requiere_version_publica' => true],
            ['clave' => 'ACTA', 'nombre' => 'Acta', 'requiere_version_publica' => true],
            ['clave' => 'DICTAMEN', 'nombre' => 'Dictamen', 'requiere_version_publica' => true],
            ['clave' => 'FALLO', 'nombre' => 'Fallo', 'requiere_version_publica' => true],

            ['clave' => 'VERSION_PUBLICA', 'nombre' => 'Versión pública', 'requiere_version_publica' => true],
        ];

        foreach ($items as $it) {
            TipoDocumento::updateOrCreate(
                ['clave' => $it['clave']],
                [
                    'nombre' => $it['nombre'],
                    'descripcion' => null,
                    'requiere_version_publica' => $it['requiere_version_publica'],
                    'activo' => true,
                ]
            );
        }
    }
}
