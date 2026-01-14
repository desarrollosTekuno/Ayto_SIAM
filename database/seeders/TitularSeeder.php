<?php

namespace Database\Seeders;

use App\Models\Catalogos\Titular;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TitularSeeder extends Seeder {
    public function run(): void {

        Titular::create([
            'nombre' => 'ADMINITRADOR',
            'apellido_paterno' => 'SISTEMA',
            'apellido_materno' => 'SIAM',
            'correo' => 'correo@ayto.gob.mx',
            'telefono' => '2221234567',
            'extension' => '101',
            'cargo_id' => 1,
        ]);
    }
}
