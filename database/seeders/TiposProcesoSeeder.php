<?php

namespace Database\Seeders;

use App\Models\Configuracion\TiposProceso;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiposProcesoSeeder extends Seeder {
    public function run(): void {
        $items = [
            [
                'clave' => 'REQ',
                'nombre' => 'Requisicion',
                'descripcion' => 'Proceso de requisiciones',
                'activo' => true,
            ],
            [
                'clave' => 'PASS',
                'nombre' => 'PASS',
                'descripcion' => 'Proceso PASS',
                'activo' => true,
            ],
        ];

        foreach ($items as $item) {
            TiposProceso::updateOrCreate(
                    ['clave' => $item['clave']],
                $item
            );
        }
    }
}
