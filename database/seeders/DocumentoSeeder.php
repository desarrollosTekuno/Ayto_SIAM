<?php

namespace Database\Seeders;

use App\Models\Documentos\Documento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentoSeeder extends Seeder {

    public function run(): void {
        $items = [
            [
                'clave' => 'REQ_OFICIO_SOLICITUD',
                'nombre' => 'Oficio de solicitud de compra',
                'descripcion' => 'Oficio mediante el cual la Dependencia solicita la adquisicion',
                'usado_en' => 'REQUISICIONES',
                'obligatorio' => true,
                'orden' => 1,
                'activo' => true,
            ],
            [
                'clave' => 'REQ_AUT_PRESUPUESTAL',
                'nombre' => 'Oficio de autorizacion presupuestal',
                'descripcion' => 'Documento que acredita suficiencia/autorizacion presupuestal vigente',
                'usado_en' => 'REQUISICIONES',
                'obligatorio' => true,
                'orden' => 2,
                'activo' => true,
            ],
            [
                'clave' => 'REQ_COTIZACION_BASE',
                'nombre' => 'Cotizacion base',
                'descripcion' => 'Cotizacion utilizada para determinar el importe de base presupuestal',
                'usado_en' => 'REQUISICIONES',
                'obligatorio' => true,
                'orden' => 3,
                'activo' => true,
            ],
            [
                'clave' => 'REQ_ANEXOS',
                'nombre' => 'Anexos',
                'descripcion' => 'Documentacion complementaria requerida por el bien o servicio',
                'usado_en' => 'REQUISICIONES',
                'obligatorio' => false,
                'orden' => 99,
                'activo' => true,
            ],
        ];

        foreach ($items as $item) {
            Documento::updateOrCreate(
                ['clave' => $item['clave']],
                $item
            );
        }
    }
}
