<?php

namespace Database\Seeders;

use App\Models\Catalogos\UnidadAdministrativa;
use App\Models\Catalogos\UnidadAdministrativaDato;
use App\Models\Catalogos\UnidadAdministrativaDireccion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnidadAdministrativaSeeder extends Seeder {

    public function run(): void {
        $dependenciaId = 1;

        $unidadPadre = UnidadAdministrativa::create([
            'nombre' => 'EL. UNIDAD ADM',
            'siglas' => 'DA',
            'abreviatura' => 'DIR ADM',
            'alias' => 'Administracion',
            'tipo' => 0,
            'unidad_padre_id' => null,
            'dependencia_id' => $dependenciaId,
        ]);

        UnidadAdministrativaDato::create([
            'telefono' => '2221234567',
            'extension' => '301',
            'unidad_administrativa_id' => $unidadPadre->id,
        ]);

        UnidadAdministrativaDireccion::create([
            'calle' => 'Av Reforma',
            'numero_exterior' => '150',
            'numero_interior' => null,
            'colonia' => 'Centro',
            'codigo_postal' => '72000',
            'estado_id' => null,
            'municipio_id' => null,
            'unidad_administrativa_id' => $unidadPadre->id,
        ]);
    }
}
