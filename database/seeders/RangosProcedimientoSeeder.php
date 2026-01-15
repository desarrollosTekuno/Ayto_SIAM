<?php

namespace Database\Seeders;

use App\Models\Catalogos\TipoProcedimiento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RangoProcedimientoSeeder extends Seeder {

    public function run(): void {
        $anio = DB::table('años_fiscales')->where('activo', true)->first();
        if (!$anio) {
            return;
        }

        // Tipos esperados
        $tipos = TipoProcedimiento::whereIn('clave', [
            'AD',   // AdDirecta
            'I3P',  // inv a cuando menos tres
            'LPF',  // lic publica Federal
            'LPE',  // lic pub estatal
            'LPM',  // Licitacion publica municipal
        ])->get()->keyBy('clave');

        $items = [
            ['clave' => 'AD',  'orden' => 10],
            ['clave' => 'I3P', 'orden' => 20],
            ['clave' => 'LPF', 'orden' => 30],
            ['clave' => 'LPE', 'orden' => 40],
            ['clave' => 'LPM', 'orden' => 50],
        ];

        foreach ($items as $it) {
            if (!isset($tipos[$it['clave']])) continue;

            DB::table('rangos_procedimientos')->updateOrInsert(
                [
                    'anio_fiscal_id' => $anio->id,
                    'tipo_procedimiento_id' => $tipos[$it['clave']]->id,
                ],
                [
                    'limite_inferior' => 0,
                    'limite_superior' => 0,
                    'orden' => $it['orden'],
                    'activo' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
