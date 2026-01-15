<?php

namespace Database\Seeders;

use App\Models\Catalogos\Cargo;
use App\Models\Catalogos\Etapa;
use App\Models\Catalogos\TipoProcedimiento;
use App\Models\Configuracion\ProcedimientoEtapaResponsable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProcedimientoEtapaResponsableSeeder extends Seeder {

    public function run(): void {
        $tp = fn(string $c) => TipoProcedimiento::where('clave', $c)->first();
        $et = fn(string $c) => Etapa::where('clave', $c)->first();

        $cargoAnalista = Cargo::where('nombre', 'ANALISTA')->first();
        $cargoJefe = Cargo::where('nombre', 'JEFE DE DEPARTAMENTO')->first();
        $cargoDirector = Cargo::where('nombre', 'DIRECTOR')->first();

        $map = [

            ['AD',  'CAPTURA_REQUISICION',         $cargoAnalista, 10, false, null],
            ['AD',  'REVISION_REQUISICION',        $cargoJefe,     10, false, null],
            ['AD',  'AUTORIZACION_REQUISICION',    $cargoDirector, 10, true,  'AUTORIZA'],
            ['AD',  'INTEGRACION_EXPEDIENTE',      $cargoAnalista, 10, false, null],
            ['AD',  'REVISION_EXPEDIENTE',         $cargoJefe,     10, false, null],
            ['AD',  'PUBLICACION',                 $cargoDirector, 10, true,  'VB'],
        ];

        foreach ($map as [$tipo, $etapa, $cargo, $orden, $firma, $tipoFirma]) {
            if (!$cargo) continue;

            $tipoModel = $tp($tipo);
            $etapaModel = $et($etapa);
            if (!$tipoModel || !$etapaModel) continue;

            ProcedimientoEtapaResponsable::updateOrCreate(
                [
                    'tipo_procedimiento_id' => $tipoModel->id,
                    'etapa_id' => $etapaModel->id,
                    'cargo_id' => $cargo->id,
                ],
                [
                    'orden' => $orden,
                    'requiere_firma' => $firma,
                    'tipo_firma' => $tipoFirma,
                    'activo' => true,
                ]
            );
        }
    }
}
