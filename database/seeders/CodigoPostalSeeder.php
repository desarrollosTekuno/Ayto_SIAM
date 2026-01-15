<?php

namespace Database\Seeders;

use App\Models\Catalogos\Colonia;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CodigoPostalSeeder extends Seeder {

    public function run(): void {

        $url = 'https://www.correosdemexico.gob.mx/datosabiertos/cp/cpdescarga.csv';

        $response = Http::timeout(120)->retry(3, 1500)->get($url);

        if (!$response->successful()) {
            throw new \RuntimeException("No se pudo descargar CP SEPOMEX: {$response->status()}");
        }

        $lines = preg_split("/\r\n|\n|\r/", trim($response->body()));
        if (count($lines) < 2) {
            throw new \RuntimeException('El CSV SEPOMEX no contiene filas.');
        }

        $header = str_getcsv(array_shift($lines));
        $map = [];
        foreach ($header as $idx => $col) {
            $map[trim($col)] = $idx;
        }

        $required = ['d_codigo','d_asenta','d_tipo_asenta','c_estado','c_mnpio','D_mnpio','d_estado','d_zona'];
        foreach (['d_codigo','d_asenta','d_tipo_asenta','c_estado','c_mnpio','D_mnpio','d_estado'] as $col) {
            if (!array_key_exists($col, $map)) {
                throw new \RuntimeException("Falta columna {$col} en CSV SEPOMEX.");
            }
        }

        $estados = DB::table('estados')
            ->select('id', 'clave')
            ->whereNull('deleted_at')
            ->get()
            ->keyBy(fn($e) => (string)intval($e->clave));

        $municipios = DB::table('municipios')
            ->select('id', 'estado_id', 'clave')
            ->whereNull('deleted_at')
            ->get()
            ->groupBy(fn($m) => $m->estado_id)
            ->map(function ($group) {
                return $group->keyBy(fn($m) => (string)intval($m->clave));
            });

        $now = now();

        DB::transaction(function () use ($lines, $map, $estados, $municipios, $now) {

            foreach ($lines as $line) {
                if (!trim($line)) continue;

                $row = str_getcsv($line);

                $cp = $this->norm($row[$map['d_codigo']] ?? null);
                $asenta = $this->norm($row[$map['d_asenta']] ?? null);
                $tipoAsenta = $this->norm($row[$map['d_tipo_asenta']] ?? null);

                $cEstado = $row[$map['c_estado']] ?? null;
                $cMnpio  = $row[$map['c_mnpio']] ?? null;

                if ((int)$cEstado !== 21 || (int)$cMnpio !== 114) {
                    continue;
                }

                $zona = $map['d_zona'] ?? null;
                $dZona = $zona !== null ? ($row[$zona] ?? null) : null;

                if (!$cp || !$asenta || !$cEstado || !$cMnpio) continue;

                $cp = str_pad((string)$cp, 5, '0', STR_PAD_LEFT);
                $cEstadoKey = (string)intval($cEstado);
                $cMnpioKey  = (string)intval($cMnpio);

                $estado = $estados[$cEstadoKey] ?? null;
                if (!$estado) continue;

                $munMap = $municipios[$estado->id] ?? null;
                $municipio = $munMap ? ($munMap[$cMnpioKey] ?? null) : null;
                if (!$municipio) continue;

                $coloniaId = Colonia::query()
                    ->whereNull('deleted_at')
                    ->where('municipio_id', $municipio->id)
                    ->where('nombre', $asenta)
                    ->value('id');

                if (!$coloniaId) {
                    $coloniaId = Colonia::insertGetId([
                        'municipio_id' => $municipio->id,
                        'nombre' => $asenta,
                        'tipo' => $tipoAsenta,
                        'created_at' => $now,
                        'updated_at' => $now,
                        'deleted_at' => null,
                    ]);
                }

                DB::table('codigos_postales')->updateOrInsert(
                    [
                        'cp' => $cp,
                        'colonia_id' => $coloniaId,
                    ],
                    [
                        'asentamiento' => $asenta,
                        'tipo_asentamiento' => $tipoAsenta,
                        'zona' => $dZona,
                        'estado_id' => $estado->id,
                        'municipio_id' => $municipio->id,
                        'updated_at' => $now,
                        'created_at' => $now,
                        'deleted_at' => null,
                    ]
                );
            }
        });
    }

    private function toUtf8(?string $value): ?string {
        if ($value === null) return null;

        $value = trim($value);
        if ($value === '') return null;

        // Si ya es UTF-8 válido, se queda.
        if (mb_check_encoding($value, 'UTF-8')) {
            return $value;
        }

        $converted = @mb_convert_encoding($value, 'UTF-8', ['Windows-1252', 'ISO-8859-1', 'UTF-8']);

        return mb_convert_encoding($converted, 'UTF-8', 'UTF-8');
    }

    private function norm(?string $value): ?string {
        $value = $this->toUtf8($value);
        if ($value === null) return null;

        $value = preg_replace('/\s+/u', ' ', $value);
        return trim($value);
    }
}
