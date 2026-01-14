<?php

namespace Database\Seeders;

use App\Models\Catalogos\Secretaria;
use App\Models\Catalogos\SecretariaDato;
use App\Models\Catalogos\SecretariaDireccion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SecretariaSeeder extends Seeder {


    public function run(): void {

        $secretariaPadre = Secretaria::create([
            'nombre' => 'SECRETARIA DE ADMINISTRACION',
            'cveDep' => 'ADM',
            'cveURes' => '0001',
            'abreviatura' => 'SA',
            'tipo' => 0,
            'usado_en' => 'SIAM',
            'secretaria_padre_id' => null,
        ]);

        SecretariaDato::create([
            'telefono' => '2221234567',
            'extension' => '101',
            'titular_id' => 1,
            'secretaria_id' => $secretariaPadre->id,
        ]);

        SecretariaDireccion::create([
            'calle' => 'Av Reforma',
            'numero_exterior' => '100',
            'numero_interior' => null,
            'colonia' => 'Centro',
            'codigo_postal' => '72000',
            'secretaria_id' => $secretariaPadre->id,
            'estado_id' => 21,
            'municipio_id' => 1,
        ]);

        $secretariaHija = Secretaria::create([
            'nombre' => 'SECRETARIA DE ADMINISTRACION INTERNA',
            'cveDep' => 'RH',
            'cveURes' => '0002',
            'abreviatura' => 'DRH',
            'tipo' => 1,
            'usado_en' => 'SIAM',
            'secretaria_padre_id' => $secretariaPadre->id,
        ]);

        SecretariaDato::create([
            'telefono' => '2227654321',
            'extension' => '202',
            'titular_id' => 1,
            'secretaria_id' => $secretariaHija->id,
        ]);

        SecretariaDireccion::create([
            'calle' => 'Av Reforma',
            'numero_exterior' => '102',
            'numero_interior' => '2A',
            'colonia' => 'Centro',
            'codigo_postal' => '72000',
            'secretaria_id' => $secretariaHija->id,
            'estado_id' => 21,
            'municipio_id' => 1,
        ]);
    }
}
