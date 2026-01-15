<?php

namespace Database\Seeders;

use App\Models\Catalogos\TipoProcedimiento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RangoProcedimientoSeeder extends Seeder {

    public function run(): void {

        $anio = DB::table('años_fiscales')->where('activo', true)->first()
            ?? DB::table('años_fiscales')->orderByDesc('año')->first();

        if (!$anio) {
            return;
        }

        $tipos = TipoProcedimiento::whereIn('clave', [
            'AD',
            'I3P',
            'CPI',
            'LPF',
            'LPE',
            'LPM',
        ])->get()->keyBy('clave');



        $items = [
            ['clave' => 'AD',  'orden' => 10, 'limite_inferior' => 0.00,        'limite_superior' => 39999.99],
            ['clave' => 'I3P', 'orden' => 20, 'limite_inferior' => 40000.00,    'limite_superior' => 1178573.87],
            ['clave' => 'CPI', 'orden' => 30, 'limite_inferior' => 200000.87,   'limite_superior' => 2678577.00],
            ['clave' => 'LPF', 'orden' => 40, 'limite_inferior' => 2678577.01,  'limite_superior' => 2678577.02],
            ['clave' => 'LPE', 'orden' => 50, 'limite_inferior' => 2678577.01,  'limite_superior' => 2678577.02],
            ['clave' => 'LPM', 'orden' => 60, 'limite_inferior' => 2678577.01,  'limite_superior' => 2678577.02],
        ];

        foreach ($items as $it) {

            $tipo = $tipos->get($it['clave']);
            if (!$tipo) {
                continue;
            }

            DB::table('rangos_procedimientos')->updateOrInsert(
                [
                    'anio_fiscal_id' => $anio->id,
                    'tipo_procedimiento_id' => $tipo->id,
                ],
                [
                    'limite_inferior' => $it['limite_inferior'],
                    'limite_superior' => $it['limite_superior'],
                    'orden' => $it['orden'],
                    'activo' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
