<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class EstadosSeeder extends Seeder {

    public function run(): void {
        $url = 'https://gaia.inegi.org.mx/wscatgeo/v2/mgee/';
        $response = Http::timeout(60)->retry(3, 1000)->get($url);

        if (!$response->successful()) {
            throw new \RuntimeException("No se pudo descargar estados INEGI: {$response->status()}");
        }

        $items = $response->json();
        $datos = $items['datos'] ?? [];

        if (empty($datos)) {
            throw new \RuntimeException('La respuesta de INEGI no contiene datos.');
        }

        $now = now();

        foreach ($datos as $it) {
            DB::table('estados')->updateOrInsert(
                ['clave' => $it['cve_ent']],
                [
                    'nombre' => $it['nomgeo'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

    }

}
