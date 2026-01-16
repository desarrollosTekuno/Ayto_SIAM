<?php

namespace Database\Seeders;

use App\Models\Catalogos\ClavePresupuestal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClavePresupuestalSeeder extends Seeder {
    public function run(): void {
        $items = [
            [
                'clave' => '333-01-001-2026',
                'nombre' => 'Servicios de consultoria administrativa',
                'descripcion' => 'Capitulo 3000 servicios generales',
                'activo' => true,
            ],
        ];

        foreach ($items as $item) {
            ClavePresupuestal::updateOrCreate(
                ['clave' => $item['clave']],
                $item
            );
        }
    }
}
