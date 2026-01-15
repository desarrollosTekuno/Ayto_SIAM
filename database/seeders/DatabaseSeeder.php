<?php

namespace Database\Seeders;

use App\Models\Cargo;
use App\Models\User;
use Database\Seeders\CargosSeeder;
use Database\Seeders\EstadosSeeder;
use Database\Seeders\MunicipiosSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {

    public function run(): void {
        $this->call([
            ExamplesSeeder::class,
            RolesSeeder::class,

            EstadosSeeder::class,
            MunicipiosSeeder::class,
            UnidadMedidaSeeder::class,
            ObjetoGastoSeeder::class,

            CodigoPostalSeeder::class,
            CargosSeeder::class,
            TitularSeeder::class,
            DependenciasSeeder::class,
            SecretariaSeeder::class,
            UnidadAdministrativaSeeder::class,

            AnioFiscalSeeder::class,
            TipoProcedimientoSeeder::class,
            EtapaSeeder::class,
            EstatusExpedienteSeeder::class,

            TipoDocumentoSeeder::class,
            ExtensionArchivoSeeder::class,

            ConfiguracionSistemaSeeder::class,

            ProcedimientoEtapaSeeder::class,
            ProcedimientoEtapaResponsableSeeder::class,
            DocumentoRequeridoSeeder::class,

            RangoProcedimientoSeeder::class,

            UsersSeeder::class,
            LineamientoGeneralArchivoSeeder::class,
        ]);

    }
}
