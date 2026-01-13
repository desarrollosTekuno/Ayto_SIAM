<?php

namespace Database\Seeders;

use App\Models\Formatos\LineamientoGeneralArchivo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LineamientoGeneralArchivoSeeder extends Seeder {

    public function run(): void {
        $items = [
            ['nombre' => 'manual procedimiento', 'titulo' => 'manual de procedimiento'],
            ['nombre' => 'memo alta',            'titulo' => 'memo alta'],
            ['nombre' => 'memo baja',            'titulo' => 'memo baja'],
            ['nombre' => 'oficio alta',          'titulo' => 'oficio alta'],
            ['nombre' => 'oficio baja',          'titulo' => 'oficio baja'],
        ];

        foreach ($items as $row) {
            $nombre = strtoupper($row['nombre']);
            $titulo = strtoupper($row['titulo']);

            LineamientoGeneralArchivo::updateOrCreate(
                ['nombre' => $nombre],
                [
                    'titulo' => $titulo,
                    'archivo' => null,
                    'actualizado_por' => 1,
                ]
            );
        }
    }
}
