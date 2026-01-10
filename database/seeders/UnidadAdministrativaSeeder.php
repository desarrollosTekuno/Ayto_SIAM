<?php

namespace Database\Seeders;

use App\Models\Catalogos\UnidadAdministrativa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnidadAdministrativaSeeder extends Seeder {

    public function run(): void {
        $items = [
            ['nombre' => 'DIRECCION GENERAL'],
            ['nombre' => 'SUBDIRECCION'],
            ['nombre' => 'COORDINACION'],
        ];

        foreach ($items as $item) {
            UnidadAdministrativa::updateOrCreate(
                ['nombre' => $item['nombre']],
                $item
            );
        }
    }
}
