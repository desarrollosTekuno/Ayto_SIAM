<?php

namespace Database\Seeders;

use App\Models\Catalogos\TipoProcedimiento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoProcedimientoSeeder extends Seeder {
    public function run(): void {
        $items = [
            [
                'clave' => 'AD',
                'nombre' => 'ADJUDICACION DIRECTA',
                'ambito' => null,
                'descripcion' => 'PROCEDIMIENTO POR ADJUDICACION DIRECTA CONFORME A NORMATIVA APLICABLE.',
                'activo' => true,
            ],
            [
                'clave' => 'I3P',
                'nombre' => 'INVITACION A CUANDO MENOS TRES PERSONAS',
                'ambito' => null,
                'descripcion' => 'PROCEDIMIENTO POR INVITACION A CUANDO MENOS TRES PERSONAS.',
                'activo' => true,
            ],
            [
                'clave' => 'CPI',
                'nombre' => 'CONCURSO POR INVITACION',
                'ambito' => null,
                'descripcion' => 'PROCEDIMIENTO DE CONCURSO POR INVITACION.',
                'activo' => true,
            ],
            [
                'clave' => 'LPF',
                'nombre' => 'LICITACION PUBLICA FEDERAL',
                'ambito' => 'FEDERAL',
                'descripcion' => 'PROCEDIMIENTO DE LICITACION PUBLICA EN AMBITO FEDERAL.',
                'activo' => true,
            ],
            [
                'clave' => 'LPE',
                'nombre' => 'LICITACION PUBLICA ESTATAL',
                'ambito' => 'ESTATAL',
                'descripcion' => 'PROCEDIMIENTO DE LICITACION PUBLICA EN AMBITO ESTATAL.',
                'activo' => true,
            ],
            [
                'clave' => 'LPM',
                'nombre' => 'LICITACION PUBLICA MUNICIPAL',
                'ambito' => 'MUNICIPAL',
                'descripcion' => 'PROCEDIMIENTO DE LICITACION PUBLICA EN AMBITO MUNICIPAL.',
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
