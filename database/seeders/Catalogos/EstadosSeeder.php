<?php

namespace Database\Seeders\Catalogos;

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

        $rows = collect($items)->map(function ($it) {
            return [
                'clave'      => $it['cve_ent'] ?? null,
                'nombre'     => $it['nomgeo'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ];
        })->filter(fn ($r) => $r['clave'] && $r['nombre'])
            ->values()
            ->all();

        DB::table('estados')->upsert(
            $rows,
            ['clave'],
            ['nombre', 'updated_at', 'deleted_at']
        );
    }

}
