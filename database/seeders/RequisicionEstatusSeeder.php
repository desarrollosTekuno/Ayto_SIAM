<?php

namespace Database\Seeders;

use App\Models\Requisiciones\RequisicionEstatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RequisicionEstatusSeeder extends Seeder {
    public function run(): void {
        $items = [
            [
                'clave' => 'CAPTURADA',
                'nombre' => 'Capturada',
                'descripcion' => 'Requisicion en captura por la Dependencia',
                'orden' => 1,
                'activo' => true,
            ],
            [
                'clave' => 'ENVIADA',
                'nombre' => 'Enviada',
                'descripcion' => 'Requisicion enviada a revision',
                'orden' => 2,
                'activo' => true,
            ],
            [
                'clave' => 'OBSERVADA',
                'nombre' => 'Observada',
                'descripcion' => 'Requisicion con observaciones por solventar',
                'orden' => 3,
                'activo' => true,
            ],
            [
                'clave' => 'AUTORIZADA',
                'nombre' => 'Autorizada',
                'descripcion' => 'Requisicion autorizada y lista para turnarse',
                'orden' => 4,
                'activo' => true,
            ],
            [
                'clave' => 'RECHAZADA',
                'nombre' => 'Rechazada',
                'descripcion' => 'Requisicion rechazada de forma definitiva',
                'orden' => 5,
                'activo' => true,
                'es_final' => true,
            ],
            [
                'clave' => 'TURNADA',
                'nombre' => 'Turnada',
                'descripcion' => 'Requisicion turnada al procedimiento de adjudicacion',
                'orden' => 6,
                'activo' => true,
                'es_final' => true,
            ],
        ];

        foreach ($items as $item) {
            RequisicionEstatus::updateOrCreate(
                ['clave' => $item['clave']],
                $item
            );
        }
    }
}
