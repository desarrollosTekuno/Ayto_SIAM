<?php

namespace Database\Seeders;

use App\Models\Catalogos\ExtensionArchivo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExtensionArchivoSeeder extends Seeder {

    public function run(): void {
        $items = [
            ['extension' => 'pdf',  'mime' => 'application/pdf', 'descripcion' => 'DOCUMENTO PDF'],

            ['extension' => 'doc',  'mime' => 'application/msword', 'descripcion' => 'DOCUMENTO WORD'],
            ['extension' => 'docx', 'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'descripcion' => 'DOCUMENTO WORD'],

            ['extension' => 'xls',  'mime' => 'application/vnd.ms-excel', 'descripcion' => 'HOJA DE CALCULO EXCEL'],
            ['extension' => 'xlsx', 'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'descripcion' => 'HOJA DE CALCULO EXCEL'],

            ['extension' => 'ppt',  'mime' => 'application/vnd.ms-powerpoint', 'descripcion' => 'PRESENTACION POWERPOINT'],
            ['extension' => 'pptx', 'mime' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'descripcion' => 'PRESENTACION POWERPOINT'],

            ['extension' => 'txt',  'mime' => 'text/plain', 'descripcion' => 'ARCHIVO DE TEXTO'],

            ['extension' => 'png',  'mime' => 'image/png', 'descripcion' => 'IMAGEN PNG'],
            ['extension' => 'jpg',  'mime' => 'image/jpeg', 'descripcion' => 'IMAGEN JPG'],
            ['extension' => 'jpeg', 'mime' => 'image/jpeg', 'descripcion' => 'IMAGEN JPEG'],
            ['extension' => 'gif',  'mime' => 'image/gif', 'descripcion' => 'IMAGEN GIF'],

            ['extension' => 'avi',  'mime' => 'video/x-msvideo', 'descripcion' => 'VIDEO AVI'],
            ['extension' => 'mp4',  'mime' => 'video/mp4', 'descripcion' => 'VIDEO MP4'],

            ['extension' => 'inf',  'mime' => 'application/octet-stream', 'descripcion' => 'ARCHIVO INF'],
            ['extension' => 'lnk',  'mime' => 'application/octet-stream', 'descripcion' => 'ACCESO DIRECTO LNK'],
        ];

        foreach ($items as $it) {
            ExtensionArchivo::updateOrCreate(
                ['extension' => strtolower($it['extension'])],
                [
                    'mime' => $it['mime'],
                    'descripcion' => strtoupper($it['descripcion']),
                    'activo' => true,
                ]
            );
        }
    }
}
