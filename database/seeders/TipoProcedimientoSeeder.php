<?php

namespace Database\Seeders;

use App\Models\Catalogos\TipoProcedimiento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoProcedimientoSeeder extends Seeder {
    public function run(): void
    {
        $items = [
            [
                'clave' => 'AD',
                'nombre' => 'Adjudicación directa',
                'ambito' => null,
                'descripcion' => 'Procedimiento por adjudicación directa conforme a normativa aplicable.',
                'activo' => true,
            ],
            [
                'clave' => 'I3P',
                'nombre' => 'Invitación a cuando menos tres personas',
                'ambito' => null,
                'descripcion' => 'Procedimiento por invitación a cuando menos tres personas.',
                'activo' => true,
            ],
            [
                'clave' => 'LPF',
                'nombre' => 'Licitación pública federal',
                'ambito' => 'FEDERAL',
                'descripcion' => 'Procedimiento de licitación pública en ámbito federal.',
                'activo' => true,
            ],
            [
                'clave' => 'LPE',
                'nombre' => 'Licitación pública estatal',
                'ambito' => 'ESTATAL',
                'descripcion' => 'Procedimiento de licitación pública en ámbito estatal.',
                'activo' => true,
            ],
            [
                'clave' => 'LPM',
                'nombre' => 'Licitación pública municipal',
                'ambito' => 'MUNICIPAL',
                'descripcion' => 'Procedimiento de licitación pública en ámbito municipal.',
                'activo' => true,
            ],
        ];

        foreach ($items as $it) {
            TipoProcedimiento::updateOrCreate(
                ['clave' => $it['clave']],
                $it
            );
        }
    }
}
