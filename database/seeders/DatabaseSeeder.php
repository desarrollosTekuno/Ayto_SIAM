<?php

namespace Database\Seeders;

use App\Models\Catalogos\Cargo;
use App\Models\User;
use Database\Seeders\Catalogos\CargosSeeder;
use Database\Seeders\Catalogos\EstadosSeeder;
use Database\Seeders\Catalogos\MunicipiosSeeder;
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
            EstadosSeeder::class,
            MunicipiosSeeder::class,
            CargosSeeder::class,
            DependenciasSeeder::class,
            UnidadAdministrativaSeeder::class,
            ObjetoGastoSeeder::class,

            ConfiguracionSistemaSeeder::class,
            RangosProcedimientoSeeder::class,
            UsersSeeder::class,
        ]);
    }
}
