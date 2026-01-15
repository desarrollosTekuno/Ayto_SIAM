<?php

namespace Database\Seeders;

use App\Models\Catalogos\EstatusExpediente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstatusExpedienteSeeder extends Seeder {
    public function run(): void {
        $items = [
            ['clave' => 'CAPTURADO',   'nombre' => 'Capturado',   'orden' => 10],
            ['clave' => 'EN_REVISION', 'nombre' => 'En revisión', 'orden' => 20],
            ['clave' => 'OBSERVADO',   'nombre' => 'Observado',   'orden' => 30],
            ['clave' => 'AUTORIZADO',  'nombre' => 'Autorizado',  'orden' => 40],
            ['clave' => 'PUBLICADO',   'nombre' => 'Publicado',   'orden' => 50],
            ['clave' => 'CERRADO',     'nombre' => 'Cerrado',     'orden' => 60],
            ['clave' => 'CANCELADO',   'nombre' => 'Cancelado',   'orden' => 70],
        ];

        foreach ($items as $it) {
            EstatusExpediente::updateOrCreate(
                ['clave' => $it['clave']],
                [
                    'nombre' => $it['nombre'],
                    'descripcion' => $it['descripcion'] ?? null,
                    'color_estatus' => $it['color'] ?? null,
                    'orden' => $it['orden'],
                    'activo' => true,
                ]
            );
        }
    }
}
