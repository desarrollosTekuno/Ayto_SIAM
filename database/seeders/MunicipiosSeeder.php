<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class MunicipiosSeeder extends Seeder {

    public function run(): void {
        $estados = DB::table('estados')->select('id', 'clave')->whereNull('deleted_at')->get();

        $rows = [];

        foreach ($estados as $estado) {
            $cveEnt = str_pad((string)$estado->clave, 2, '0', STR_PAD_LEFT);
            $url = "https://gaia.inegi.org.mx/wscatgeo/v2/mgem/{$cveEnt}";

            $response = Http::timeout(60)->retry(3, 1000)->get($url);

            if (!$response->successful()) {
                throw new \RuntimeException("No se pudo descargar municipios INEGI para {$cveEnt}: {$response->status()}");
            }

            $items = $response->json();
            $datos = $items['datos'] ?? [];

            if (empty($datos)) {
                continue;
            }

            $now = now();

            foreach ($datos as $it) {
                $nombre = $it['nomgeo'] ?? null;
                $cveMun = $it['cve_mun'] ?? null;

                if (!$nombre || !$cveMun) continue;

                DB::table('municipios')->updateOrInsert(
                    [
                        'estado_id' => $estado->id,
                        'clave' => $cveMun,
                    ],
                    [
                        'nombre' => $nombre,
                        'created_at' => $now,
                        'updated_at' => $now,
                        'deleted_at' => null,
                    ]
                );

            }
        }
    }
}
