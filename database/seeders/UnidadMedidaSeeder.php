<?php

namespace Database\Seeders;

use App\Models\Catalogos\UnidadMedida;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnidadMedidaSeeder extends Seeder {
    public function run(): void
    {
        $items = [
            ['nombre' => 'PIEZA'],
            ['nombre' => 'SERVICIO'],
            ['nombre' => 'PAQUETE'],
            ['nombre' => 'METRO'],
            ['nombre' => 'KILOGRAMO'],
        ];

        foreach ($items as $item) {
            UnidadMedida::updateOrCreate(
                ['nombre' => $item['nombre']],
                $item
            );
        }
    }
}
