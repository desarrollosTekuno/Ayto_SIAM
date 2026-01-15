<?php

namespace Database\Seeders;

use App\Models\Configuracion\TiposProceso;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiposProcesoSeeder extends Seeder {
    public function run(): void {
        $items = [
            [
                'clave' => 'ADQ',
                'nombre' => 'Adquisiciones',
                'descripcion' => 'Procesos relacionados con la adquisicion de bienes',
                'activo' => true,
            ],
            [
                'clave' => 'SRV',
                'nombre' => 'Servicios',
                'descripcion' => 'Contratacion de servicios',
                'activo' => true,
            ],
            [
                'clave' => 'OBR',
                'nombre' => 'Obra publica',
                'descripcion' => 'Procesos de obra publica',
                'activo' => true,
            ],
            [
                'clave' => 'ARR',
                'nombre' => 'Arrendamientos',
                'descripcion' => 'Arrendamiento de bienes y servicios',
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
