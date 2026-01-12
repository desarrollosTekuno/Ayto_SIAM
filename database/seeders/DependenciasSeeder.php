<?php

namespace Database\Seeders;

use App\Models\Catalogos\Area;
use App\Models\Catalogos\Departamento;
use App\Models\Catalogos\Dependencia;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DependenciasSeeder extends Seeder {

    public function run(): void {
        $path = database_path('seeders/data/DependenciasEntidades.xlsx');

        if (!file_exists($path)) {
            throw new \RuntimeException("No existe el archivo: {$path}");
        }

        $sheet = IOFactory::load($path)->getActiveSheet();
        $rows  = $sheet->toArray(null, true, true, true);

        if (count($rows) < 2) {
            $this->command?->warn('El Excel no trae filas de datos.');
            return;
        }

        $header = array_shift($rows);

        $colCveDep  = null;
        $colCveURes = null;
        $colNombre  = null;

        foreach ($header as $col => $title) {
            $t = mb_strtoupper(trim((string)$title));

            if ($t === 'CVEDEP')   $colCveDep  = $col;
            if ($t === 'CVEURES')  $colCveURes = $col;
            if ($t === 'NOMBRE' || $t === 'DEPENDENCIA') $colNombre = $col;
        }

        if (!$colNombre) {
            throw new \RuntimeException('Falta columna requerida: nombre.');
        }

        $created = 0;
        $updated = 0;

        $seen = [];

        foreach ($rows as $row) {
            $nombre = $this->norm($row[$colNombre] ?? '');
            if ($nombre === '') continue;

            if (isset($seen[$nombre])) continue;
            $seen[$nombre] = true;

            $cveDep  = $this->norm($row[$colCveDep] ?? '');
            $cveURes = $this->norm($row[$colCveURes] ?? '');

            $dep = Dependencia::withTrashed()->updateOrCreate(
                ['nombre' => $nombre],
                [
                    'cveDep'      => $cveDep  !== '' ? $cveDep  : null,
                    'cveURes'     => $cveURes !== '' ? $cveURes : null,
                    'abreviatura' => null,
                    'usado_en'    => 'DP',
                    'deleted_at'  => null,
                ]
            );

            $dep->wasRecentlyCreated ? $created++ : $updated++;
        }

        $this->command?->info("Dependencias listas. Creadas: {$created}, Actualizadas: {$updated}");
    }

    private function norm($value): string {
        $s = trim((string)$value);
        if ($s === '') return '';
        $s = preg_replace('/\s+/u', ' ', $s);
        return mb_strtoupper($s);
    }
}
