<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {

    public function run(): void {
        $this->call([
            ExamplesSeeder::class,
            RolesSeeder::class,
            TiposProcesoSeeder::class,
            UnidadMedidaSeeder::class,
            ConfiguracionSistemaSeeder::class,
            DependenciasSeeder::class,
            // DependenciaSeeder::class,
            UnidadAdministrativaSeeder::class,
            RangosProcedimientoSeeder::class,
            UsersSeeder::class,
        ]);
    }
}
