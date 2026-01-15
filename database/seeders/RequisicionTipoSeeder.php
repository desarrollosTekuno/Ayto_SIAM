<?php

namespace Database\Seeders;

use App\Models\Requisiciones\RequisicionTipo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RequisicionTipoSeeder extends Seeder {

    public function run(): void {
        $items = [
            [
                'clave' => 'BIENES',
                'nombre' => 'Bienes',
                'descripcion' => 'Adquisicion de bienes materiales',
                'orden' => 1,
                'activo' => true,
            ],
            [
                'clave' => 'SERVICIOS',
                'nombre' => 'Servicios',
                'descripcion' => 'Contratacion de servicios',
                'orden' => 2,
                'activo' => true,
            ],
            [
                'clave' => 'ARRENDAMIENTO',
                'nombre' => 'Arrendamiento',
                'descripcion' => 'Arrendamiento de bienes o servicios relacionados',
                'orden' => 3,
                'activo' => true,
            ],
        ];

        foreach ($items as $item) {
            RequisicionTipo::updateOrCreate(
                ['clave' => $item['clave']],
                $item
            );
        }
    }
}
