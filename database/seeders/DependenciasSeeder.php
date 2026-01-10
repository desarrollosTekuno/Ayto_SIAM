<?php

namespace Database\Seeders;

use App\Models\Catalogos\Dependencia;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DependenciasSeeder extends Seeder {

    public function run(): void {
        $path = database_path('seeders/data/CatalogosDp.xlsx');

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
        $colDep = $colDpto = $colArea = null;

        foreach ($header as $col => $title) {
            $t = mb_strtoupper(trim((string)$title));
            if ($t === 'DEPENDENCIA')  $colDep  = $col;
            if ($t === 'DEPARTAMENTO') $colDpto = $col;
            if ($t === 'AREA')         $colArea = $col;
        }

        if (!$colDep || !$colDpto || !$colArea) {
            throw new \RuntimeException('Faltan columnas requeridas: DEPENDENCIA / DEPARTAMENTO / AREA.');
        }

        $depCache  = [];
        $areaCache = [];

        $createdDep = $updatedDep = 0;
        $createdArea = $updatedArea = 0;
        $createdDpto = $updatedDpto = 0;

        foreach ($rows as $idx => $row) {
            $depNombre  = $this->norm($row[$colDep] ?? '');
            $dptoNombre = $this->norm($row[$colDpto] ?? '');
            $areaNombre = $this->norm($row[$colArea] ?? '');

            if ($dptoNombre === '') {
                continue;
            }

            $dependenciaId = null;
            if ($depNombre !== '') {
                if (!isset($depCache[$depNombre])) {
                    $dep = Dependencia::withTrashed()->updateOrCreate(
                        ['nombre' => $depNombre],
                        [
                            'abreviatura' => null,
                            'alias'       => null,
                            'usado_en'    => 'DP',
                            'deleted_at'  => null,
                        ]
                    );

                    $depCache[$depNombre] = $dep->id;
                    $dep->wasRecentlyCreated ? $createdDep++ : $updatedDep++;
                }
                $dependenciaId = $depCache[$depNombre];
            }

            $areaId = null;
            if ($areaNombre !== '') {
                if (!isset($areaCache[$areaNombre])) {
                    $area = Areas::withTrashed()->updateOrCreate(
                        ['nombre' => $areaNombre],
                        [
                            'abreviatura'      => null,
                            'alias'            => null,
                            'usado_en'         => 'DP',
                            'ayto_biometricos' => true,
                            'deleted_at'       => null,
                        ]
                    );

                    $areaCache[$areaNombre] = $area->id;
                    $area->wasRecentlyCreated ? $createdArea++ : $updatedArea++;
                }
                $areaId = $areaCache[$areaNombre];
            }

            $dpto = Departamentos::withTrashed()->updateOrCreate(
                ['nombre' => $dptoNombre, 'usado_en' => 'DP'],
                [
                    'abreviatura'      => null,
                    'alias'            => null,
                    'ayto_biometricos' => true,
                    'area_id'          => $areaId,
                    'dependencia_id'   => $dependenciaId,
                    'empresa_id'       => null,
                    'deleted_at'       => null,
                ]
            );

            $dpto->wasRecentlyCreated ? $createdDpto++ : $updatedDpto++;
        }

        $this->command?->info(
            "Catálogos DP listos.\n" .
            "Dependencias -> Creadas: {$createdDep}, Actualizadas: {$updatedDep}\n" .
            "Áreas        -> Creadas: {$createdArea}, Actualizadas: {$updatedArea}\n" .
            "Departamentos-> Creados: {$createdDpto}, Actualizados: {$updatedDpto}"
        );
    }

    private function norm($value): string
    {
        $s = trim((string)$value);
        if ($s === '') return '';
        $s = preg_replace('/\s+/u', ' ', $s);
        return mb_strtoupper($s);
    }
}
