<?php

namespace Database\Seeders;

use App\Models\Catalogos\ExtensionArchivo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExtensionArchivoSeeder extends Seeder {

    public function run(): void {
        $items = [
            ['extension' => 'pdf',  'mime' => 'application/pdf', 'descripcion' => 'Documento PDF'],
            ['extension' => 'doc',  'mime' => 'application/msword', 'descripcion' => 'Documento Word'],
            ['extension' => 'docx', 'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'descripcion' => 'Documento Word'],
            ['extension' => 'xls',  'mime' => 'application/vnd.ms-excel', 'descripcion' => 'Hoja de cálculo Excel'],
            ['extension' => 'xlsx', 'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'descripcion' => 'Hoja de cálculo Excel'],
            ['extension' => 'png',  'mime' => 'image/png', 'descripcion' => 'Imagen PNG'],
            ['extension' => 'jpg',  'mime' => 'image/jpeg', 'descripcion' => 'Imagen JPG'],
            ['extension' => 'jpeg', 'mime' => 'image/jpeg', 'descripcion' => 'Imagen JPEG'],
        ];

        foreach ($items as $it) {
            ExtensionArchivo::updateOrCreate(
                ['extension' => $it['extension']],
                [
                    'mime' => $it['mime'],
                    'descripcion' => $it['descripcion'],
                    'activo' => true,
                ]
            );
        }
    }
}
