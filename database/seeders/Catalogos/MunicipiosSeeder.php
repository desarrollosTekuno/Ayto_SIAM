<?php

namespace Database\Seeders\Catalogos;

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

            foreach ($items as $it) {
                $nombre = $it['nomgeo'] ?? null;
                $cveMun = $it['cve_mun'] ?? null;

                if (!$nombre || !$cveMun) continue;

                $rows[] = [
                    'estado_id'   => $estado->id,
                    'nombre'      => $nombre,
                    'clave'         => $cveMun,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                    'deleted_at'  => null,
                ];

            }
        }

        DB::table('municipios')->upsert(
            $rows,
            ['estado_id', 'nombre'],
            ['updated_at', 'deleted_at']
        );
    }
}
