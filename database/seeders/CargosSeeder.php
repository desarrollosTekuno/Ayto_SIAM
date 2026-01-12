<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CargosSeeder extends Seeder {
    public function run(): void {
        $cargos = [
            'ADMINISTRADOR',
        ];

        $rows = collect($cargos)->map(fn ($nombre) => [
            'nombre' => $nombre,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ])->toArray();

        DB::table('cargos')->insert($rows);
    }
}
