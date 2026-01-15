<?php

namespace Database\Seeders;

use App\Models\Configuracion\ConfiguracionSistema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfiguracionSistemaSeeder extends Seeder {
    public function run(): void {
        $items = [
            ['clave' => 'sesion_minutos_timeout', 'tipo' => 'int', 'grupo' => 'sesion', 'descripcion' => 'Tiempo máximo de sesión (minutos).'],

            ['clave' => 'captura_requisiciones_inicio', 'tipo' => 'datetime', 'grupo' => 'captura', 'descripcion' => 'Inicio de captura de requisiciones.'],
            ['clave' => 'captura_requisiciones_fin',    'tipo' => 'datetime', 'grupo' => 'captura', 'descripcion' => 'Fin de captura de requisiciones.'],
            ['clave' => 'captura_paaas_inicio',         'tipo' => 'datetime', 'grupo' => 'captura', 'descripcion' => 'Inicio de captura PAAAS.'],
            ['clave' => 'captura_paaas_fin',            'tipo' => 'datetime', 'grupo' => 'captura', 'descripcion' => 'Fin de captura PAAAS.'],

            ['clave' => 'hora_limite_envio_revision', 'tipo' => 'time', 'grupo' => 'captura', 'descripcion' => 'Hora límite para enviar a revisión.'],

            ['clave' => 'archivos_max_mb', 'tipo' => 'int', 'grupo' => 'archivos', 'descripcion' => 'Tamaño máximo permitido por archivo (MB).'],
            ['clave' => 'archivos_extensiones_permitidas', 'tipo' => 'list', 'grupo' => 'archivos', 'descripcion' => 'Extensiones permitidas (lista, sin JSON).'],
        ];

        foreach ($items as $it) {
            $clave = mb_strtoupper($it['clave']);
            ConfiguracionSistema::updateOrCreate(
                ['clave' => $clave],
                [
                    'clave' => $clave,
                    'tipo' => mb_strtoupper($it['tipo']),
                    'descripcion' => mb_strtoupper($it['descripcion']),
                    'grupo' => mb_strtoupper($it['grupo']),
                    'editable' => true,
                    'activo' => true,
                ]
            );
        }
    }
}
