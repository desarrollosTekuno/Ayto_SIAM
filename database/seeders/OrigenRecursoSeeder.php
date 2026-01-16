<?php

namespace Database\Seeders;

use App\Models\Catalogos\OrigenRecurso;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrigenRecursoSeeder extends Seeder {

    public function run(): void {
        $items = [
            ['clave' => 'MUNICIPAL',        'nombre' => 'Municipales',          'orden' => 1, 'activo' => true],
            ['clave' => 'ESTATAL',          'nombre' => 'Estatales',            'orden' => 2, 'activo' => true],
            ['clave' => 'FEDERAL',          'nombre' => 'Federales',            'orden' => 3, 'activo' => true],
            ['clave' => 'FEDERAL_FORTAMUN', 'nombre' => 'Federales (FORTAMUN)', 'orden' => 4, 'activo' => true],
            ['clave' => 'PROPIO',           'nombre' => 'Propios',              'orden' => 5, 'activo' => true],
        ];

        foreach ($items as $item) {
            OrigenRecurso::updateOrCreate(
                ['clave' => $item['clave']],
                $item
            );
        }
    }
}
