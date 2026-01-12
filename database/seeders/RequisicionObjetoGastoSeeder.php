<?php

namespace Database\Seeders;

use App\Models\Requisiciones\ObjetoGasto;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Database\Seeder;

class RequisicionObjetoGastoSeeder extends Seeder {

    public function run(): void {
        $path = database_path('seeders/data/ObjetosGasto.xlsx');

        if (!file_exists($path)) {
            throw new \RuntimeException("No existe el archivo: {$path}");
        }

        $sheet = IOFactory::load($path)->getActiveSheet();
        $rows  = $sheet->toArray(null, true, true, true);

        if (count($rows) < 2) {
            $this->command?->warn('El Excel no trae filas de datos.');
            return;
        }

        // ===== Headers =====
        $header = array_shift($rows);

        $colObjGasto = null;
        $colNombre   = null;

        foreach ($header as $col => $title) {
            $t = mb_strtoupper(trim((string) $title));

            if ($t === 'OBJGASTO' || $t === 'OBJ_GASTO' || $t === 'OBJETO_GASTO') $colObjGasto = $col;
            if ($t === 'NOMBRE' || $t === 'DESCRIPCION' || $t === 'DESCRIPCIÓN') $colNombre = $col;
        }

        if (!$colObjGasto || !$colNombre) {
            throw new \RuntimeException('Faltan columnas requeridas: objGasto y descripcion (o nombre).');
        }

        $created = 0;
        $updated = 0;

        foreach ($rows as $row) {
            $objGasto = $this->norm($row[$colObjGasto] ?? '');
            $nombre   = $this->norm($row[$colNombre] ?? '');

            if ($objGasto === '' || $nombre === '') {
                continue;
            }

            $registro = ObjetoGasto::withTrashed()->updateOrCreate(
                ['objGasto' => $objGasto],
                [
                    'nombre'     => $nombre,
                    'deleted_at' => null,
                ]
            );

            $registro->wasRecentlyCreated ? $created++ : $updated++;
        }

        $this->command?->info("Objetos de Gasto listos. Creados: {$created}, Actualizados: {$updated}");
    }

    private function norm($value): string {
        $s = trim((string) $value);
        if ($s === '') return '';
        $s = preg_replace('/\s+/u', ' ', $s);
        return mb_strtoupper($s);
    }
}
