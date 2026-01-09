<?php

namespace Database\Seeders;

use App\Models\Catalogos\Dependencia;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DependenciaSeeder extends Seeder {
    public function run(): void
    {
        $items = [
            ['nombre' => 'CONTRALORIA', 'abreviatura' => 'CTRL', 'alias' => 'CONTRALORIA'],
            ['nombre' => 'TESORERIA', 'abreviatura' => 'TES', 'alias' => 'TESORERIA'],
            ['nombre' => 'OBRAS PUBLICAS', 'abreviatura' => 'OP', 'alias' => 'OBRAS'],
        ];

        foreach ($items as $item) {
            Dependencia::updateOrCreate(
                ['nombre' => $item['nombre']],
                $item
            );
        }
    }
}
