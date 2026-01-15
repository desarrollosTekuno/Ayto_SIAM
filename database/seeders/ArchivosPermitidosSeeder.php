<?php

namespace Database\Seeders;

use App\Models\Catalogos\TipoProcedimiento;
use App\Models\Configuracion\ArchivoPermitido;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArchivosPermitidosSeeder extends Seeder {

    public function run(): void {
        $extensiones = [
            '7Z',
            'ACE',
            'AI',
            'AVI',
            'BAT',
            'BIN',
            'BMP',
            'COM',
            'CRW',
            'CSS',
            'CSV',
            'CUE',
            'DIVX',
            'DLL',
            'DOC',
            'DOCM',
            'DOCX',
            'EML',
            'EXE',
            'FLAC',
            'GIF',
            'HEIC',
            'HTML',
            'ICO',
            'ID',
            'IMG',
            'INF',
            'INI',
            'ISO',
            'JPEG',
            'JPG',
            'JS',
            'LNK',
            'M3U',
            'MIDI',
            'MKV',
            'MOV',
            'MP3',
            'MP4',
            'MPG',
            'MSG',
            'MSI',
            'NEF',
            'ODP',
            'ODS',
            'ODT',
            'OGG',
            'OTF',
            'PDF',
            'PHP',
            'PNG',
            'POTX',
            'PPS',
            'PPSM',
            'PPSX',
            'PPT',
            'PPTM',
            'PPTX',
            'PS1',
            'PSD',
            'RAR',
            'RAR5',
            'RTF',
            'SCR',
            'SPL',
            'SVG',
            'SWF',
            'SYS',
            'TTF',
            'TXT',
            'URL',
            'WAV',
            'WEBP',
            'WMA',
            'WMV',
            'WPL',
            'XLS',
            'XLSM',
        ];

        $activas = [
            'AVI',
            'GIF',
            'INF',
            'LNK',
            'MP4',
            'PDF',
            'XLS',
        ];

        $procedimientos = TipoProcedimiento::where('activo', true)
            ->whereNull('deleted_at')
            ->get();

        foreach ($extensiones as $ext) {
            $esActiva = in_array($ext, $activas, true);
            if ($procedimientos->isEmpty()) {
                ArchivoPermitido::updateOrCreate(
                    ['extension' => $ext],
                    [
                        'obligatorio' => false,
                        'activo' => $esActiva,
                        'tipo_procedimiento_id' => null,
                    ]
                );
                continue;
            }

            foreach ($procedimientos as $proc) {
                ArchivoPermitido::updateOrCreate(
                    [
                        'extension' => $ext,
                        'tipo_procedimiento_id' => $proc->id,
                    ],
                    [
                        'obligatorio' => false,
                        'activo' => $esActiva,
                    ]
                );
            }
        }
    }
}
