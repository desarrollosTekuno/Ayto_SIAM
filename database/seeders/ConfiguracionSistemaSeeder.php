<?php

namespace Database\Seeders;

use App\Models\Configuracion\ConfiguracionSistema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfiguracionSistemaSeeder extends Seeder {

    public function run(): void {
        $vars = [

            [
                'clave' => 'REQ_FECHA_INICIO_CAPTURA',
                'valor' => '2025-11-30',
                'tipo' => 'date',
                'grupo' => 'REQUISICIONES',
                'descripcion' => 'Fecha de inicio para captura de requisiciones',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'REQ_HORA_INICIO_CAPTURA',
                'valor' => '08:00:00',
                'tipo' => 'time',
                'grupo' => 'REQUISICIONES',
                'descripcion' => 'Hora de inicio para captura de requisiciones',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'REQ_FECHA_FIN_CAPTURA',
                'valor' => '2026-01-22',
                'tipo' => 'date',
                'grupo' => 'REQUISICIONES',
                'descripcion' => 'Fecha de termino para captura de requisiciones',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'REQ_HORA_FIN_CAPTURA',
                'valor' => '04:00:00',
                'tipo' => 'time',
                'grupo' => 'REQUISICIONES',
                'descripcion' => 'Hora de termino para captura de requisiciones',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'REQ_HORA_LIMITE_REVISION',
                'valor' => '22:00:00',
                'tipo' => 'time',
                'grupo' => 'REQUISICIONES',
                'descripcion' => 'Hora limite para enviar requisicion a estatus revision',
                'editable' => true,
                'activo' => true,
            ],

            // [
            //     'clave' => 'REQ_PREFIJO',
            //     'valor' => 'ERP',
            //     'tipo' => 'string',
            //     'grupo' => 'REQUISICIONES',
            //     'descripcion' => 'Prefijo para requisiciones',
            //     'editable' => true,
            //     'activo' => true,
            // ],

            [
                'clave' => 'REQ_LIMITE_CARACTERES_RECORTAR',
                'valor' => '100',
                'tipo' => 'int',
                'grupo' => 'REQUISICIONES',
                'descripcion' => 'Limite de caracteres para recortar',
                'editable' => true,
                'activo' => true,
            ],

            [
                'clave' => 'PASS_FECHA_INICIO_CAPTURA',
                'valor' => '2025-05-29',
                'tipo' => 'date',
                'grupo' => 'PASS',
                'descripcion' => 'Fecha de inicio para captura de PASS',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PASS_HORA_INICIO_CAPTURA',
                'valor' => '09:42:00',
                'tipo' => 'time',
                'grupo' => 'PASS',
                'descripcion' => 'Hora de inicio para captura de PASS',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PASS_FECHA_FIN_CAPTURA',
                'valor' => '2026-01-15',
                'tipo' => 'date',
                'grupo' => 'PASS',
                'descripcion' => 'Fecha de termino para captura de PASS',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PASS_HORA_FIN_CAPTURA',
                'valor' => '08:00:00',
                'tipo' => 'time',
                'grupo' => 'PASS',
                'descripcion' => 'Hora de termino para captura de PASS',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PASS_FECHA_SNAPSHOT_FIN',
                'valor' => '2023-05-04',
                'tipo' => 'date',
                'grupo' => 'PASS',
                'descripcion' => 'Fecha de termino para toma automatica (snapshot) PASS',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PASS_HORA_SNAPSHOT_FIN',
                'valor' => '11:00:00',
                'tipo' => 'time',
                'grupo' => 'PASS',
                'descripcion' => 'Hora de termino para toma automatica (snapshot) PASS',
                'editable' => true,
                'activo' => true,
            ],

            [
                'clave' => 'PROC_INV_3SECATI_LIMITE_INFERIOR',
                'valor' => '40000.00',
                'tipo' => 'decimal',
                'grupo' => 'PROCEDIMIENTOS',
                'descripcion' => 'Invitacion a cuando menos 3-SECATI (limite inferior)',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PROC_INV_3SECATI_LIMITE_SUPERIOR',
                'valor' => '250000.00',
                'tipo' => 'decimal',
                'grupo' => 'PROCEDIMIENTOS',
                'descripcion' => 'Invitacion a cuando menos 3-SECATI (limite superior)',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PROC_INV_3CMA_LIMITE_INFERIOR',
                'valor' => '260000.00',
                'tipo' => 'decimal',
                'grupo' => 'PROCEDIMIENTOS',
                'descripcion' => 'Invitacion a cuando menos 3-CMA (limite inferior)',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PROC_INV_3CMA_LIMITE_SUPERIOR',
                'valor' => '1178573.87',
                'tipo' => 'decimal',
                'grupo' => 'PROCEDIMIENTOS',
                'descripcion' => 'Invitacion a cuando menos 3-CMA (limite superior)',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PROC_CONCURSO_LIMITE_INFERIOR',
                'valor' => '1178573.87',
                'tipo' => 'decimal',
                'grupo' => 'PROCEDIMIENTOS',
                'descripcion' => 'Concurso por invitacion (limite inferior)',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PROC_CONCURSO_LIMITE_SUPERIOR',
                'valor' => '2678577.00',
                'tipo' => 'decimal',
                'grupo' => 'PROCEDIMIENTOS',
                'descripcion' => 'Concurso por invitacion (limite superior)',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PROC_LIC_PUBLICA_LIMITE_INFERIOR',
                'valor' => '2678577.01',
                'tipo' => 'decimal',
                'grupo' => 'PROCEDIMIENTOS',
                'descripcion' => 'Licitacion publica (limite inferior)',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PROC_LIC_PUBLICA_LIMITE_SUPERIOR',
                'valor' => '2678577.02',
                'tipo' => 'decimal',
                'grupo' => 'PROCEDIMIENTOS',
                'descripcion' => 'Licitacion publica (limite superior)',
                'editable' => true,
                'activo' => true,
            ],

            [
                'clave' => 'SUF_DIAS_VIGENCIA',
                'valor' => '20',
                'tipo' => 'int',
                'grupo' => 'SUFICIENCIA',
                'descripcion' => 'Dias de vigencia para suficiencia presupuestal',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'SUF_DIAS_RECORDATORIO',
                'valor' => '5',
                'tipo' => 'int',
                'grupo' => 'SUFICIENCIA',
                'descripcion' => 'Tiempo de recordatorio en vigencia de suficiencia presupuestal',
                'editable' => true,
                'activo' => true,
            ],

            [
                'clave' => 'SEG_PASSWORD_MIN_LENGTH',
                'valor' => '8',
                'tipo' => 'int',
                'grupo' => 'SEGURIDAD',
                'descripcion' => 'Limite de caracteres para contrasena',
                'editable' => true,
                'activo' => true,
            ],

            [
                'clave' => 'ARCHIVOS_TAM_MAX_MB',
                'valor' => '8',
                'tipo' => 'int',
                'grupo' => 'ARCHIVOS',
                'descripcion' => 'Tamano de archivo adjunto o anexos (MB)',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'ARCHIVOS_EXT_PERMITIDAS',
                'valor' => '["DOCX","GIF","INF","LNK","MP4","PDF","PPTX","XLSX"]',
                'tipo' => 'json',
                'grupo' => 'ARCHIVOS',
                'descripcion' => 'Extensiones permitidas para anexos',
                'editable' => true,
                'activo' => true,
            ],

            [
                'clave' => 'SESION_MINUTOS',
                'valor' => '15',
                'tipo' => 'int',
                'grupo' => 'SISTEMA',
                'descripcion' => 'Tiempo en minutos de sesion',
                'editable' => true,
                'activo' => true,
            ],

            [
                'clave' => 'EJERCICIO_FISCAL_ACTIVO',
                'valor' => '2026',
                'tipo' => 'int',
                'grupo' => 'SISTEMA',
                'descripcion' => 'Ejercicio fiscal activo',
                'editable' => true,
                'activo' => true,
            ],
        ];

        foreach ($vars as $v) {
            ConfiguracionSistema::updateOrCreate(
                ['clave' => $v['clave']],
                $v
            );
        }
    }
}
