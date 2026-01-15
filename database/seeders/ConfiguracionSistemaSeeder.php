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
                'valor' => '2026-01-01',
                'tipo' => 'date',
                'grupo' => 'REQUISICIONES',
                'descripcion' => 'FECHA DE INICIO PARA CAPTURA DE REQUISICIONES',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'REQ_HORA_INICIO_CAPTURA',
                'valor' => '08:00:00',
                'tipo' => 'time',
                'grupo' => 'REQUISICIONES',
                'descripcion' => 'HORA DE INICIO PARA CAPTURA DE REQUISICIONES',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'REQ_FECHA_FIN_CAPTURA',
                'valor' => '2026-11-20',
                'tipo' => 'date',
                'grupo' => 'REQUISICIONES',
                'descripcion' => 'FECHA DE TERMINO PARA CAPTURA DE REQUISICIONES',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'REQ_HORA_FIN_CAPTURA',
                'valor' => '04:00:00',
                'tipo' => 'time',
                'grupo' => 'REQUISICIONES',
                'descripcion' => 'HORA DE TERMINO PARA CAPTURA DE REQUISICIONES',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'REQ_HORA_LIMITE_REVISION',
                'valor' => '22:00:00',
                'tipo' => 'time',
                'grupo' => 'REQUISICIONES',
                'descripcion' => 'HORA LIMITE PARA ENVIAR REQUISICION A ESTATUS REVISION',
                'editable' => true,
                'activo' => true,
            ],

            // [
            //     'clave' => 'REQ_PREFIJO',
            //     'valor' => 'ERP',
            //     'tipo' => 'string',
            //     'grupo' => 'REQUISICIONES',
            //     'descripcion' => 'PREFIJO PARA REQUISICIONES',
            //     'editable' => true,
            //     'activo' => true,
            // ],

            [
                'clave' => 'REQ_LIMITE_CARACTERES_RECORTAR',
                'valor' => '100',
                'tipo' => 'int',
                'grupo' => 'REQUISICIONES',
                'descripcion' => 'LIMITE DE CARACTERES PARA RECORTAR',
                'editable' => true,
                'activo' => true,
            ],

            // =========================
            // PAAAS (ANTES PASS)
            // =========================
            [
                'clave' => 'PAAAS_FECHA_INICIO_CAPTURA',
                'valor' => '2026-01-01',
                'tipo' => 'date',
                'grupo' => 'PAAAS',
                'descripcion' => 'FECHA DE INICIO PARA CAPTURA DE PAAAS',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PAAAS_HORA_INICIO_CAPTURA',
                'valor' => '09:42:00',
                'tipo' => 'time',
                'grupo' => 'PAAAS',
                'descripcion' => 'HORA DE INICIO PARA CAPTURA DE PAAAS',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PAAAS_FECHA_FIN_CAPTURA',
                'valor' => '2026-11-20',
                'tipo' => 'date',
                'grupo' => 'PAAAS',
                'descripcion' => 'FECHA DE TERMINO PARA CAPTURA DE PAAAS',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PAAAS_HORA_FIN_CAPTURA',
                'valor' => '08:00:00',
                'tipo' => 'time',
                'grupo' => 'PAAAS',
                'descripcion' => 'HORA DE TERMINO PARA CAPTURA DE PAAAS',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PAAAS_FECHA_SNAPSHOT_FIN',
                'valor' => '2023-11-25',
                'tipo' => 'date',
                'grupo' => 'PAAAS',
                'descripcion' => 'FECHA DE TERMINO PARA TOMA AUTOMATICA (SNAPSHOT) PAAAS',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PAAAS_HORA_SNAPSHOT_FIN',
                'valor' => '11:00:00',
                'tipo' => 'time',
                'grupo' => 'PAAAS',
                'descripcion' => 'HORA DE TERMINO PARA TOMA AUTOMATICA (SNAPSHOT) PAAAS',
                'editable' => true,
                'activo' => true,
            ],

            [
                'clave' => 'PROC_INV_3SECATI_LIMITE_INFERIOR',
                'valor' => '40000.00',
                'tipo' => 'decimal',
                'grupo' => 'PROCEDIMIENTOS',
                'descripcion' => 'INVITACION A CUANDO MENOS 3-SECATI (LIMITE INFERIOR)',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PROC_INV_3SECATI_LIMITE_SUPERIOR',
                'valor' => '250000.00',
                'tipo' => 'decimal',
                'grupo' => 'PROCEDIMIENTOS',
                'descripcion' => 'INVITACION A CUANDO MENOS 3-SECATI (LIMITE SUPERIOR)',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PROC_INV_3CMA_LIMITE_INFERIOR',
                'valor' => '260000.00',
                'tipo' => 'decimal',
                'grupo' => 'PROCEDIMIENTOS',
                'descripcion' => 'INVITACION A CUANDO MENOS 3-CMA (LIMITE INFERIOR)',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PROC_INV_3CMA_LIMITE_SUPERIOR',
                'valor' => '1178573.87',
                'tipo' => 'decimal',
                'grupo' => 'PROCEDIMIENTOS',
                'descripcion' => 'INVITACION A CUANDO MENOS 3-CMA (LIMITE SUPERIOR)',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PROC_CONCURSO_LIMITE_INFERIOR',
                'valor' => '200000.87',
                'tipo' => 'decimal',
                'grupo' => 'PROCEDIMIENTOS',
                'descripcion' => 'CONCURSO POR INVITACION (LIMITE INFERIOR)',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PROC_CONCURSO_LIMITE_SUPERIOR',
                'valor' => '2678577.00',
                'tipo' => 'decimal',
                'grupo' => 'PROCEDIMIENTOS',
                'descripcion' => 'CONCURSO POR INVITACION (LIMITE SUPERIOR)',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PROC_LIC_PUBLICA_LIMITE_INFERIOR',
                'valor' => '2678577.01',
                'tipo' => 'decimal',
                'grupo' => 'PROCEDIMIENTOS',
                'descripcion' => 'LICITACION PUBLICA (LIMITE INFERIOR)',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'PROC_LIC_PUBLICA_LIMITE_SUPERIOR',
                'valor' => '2678577.02',
                'tipo' => 'decimal',
                'grupo' => 'PROCEDIMIENTOS',
                'descripcion' => 'LICITACION PUBLICA (LIMITE SUPERIOR)',
                'editable' => true,
                'activo' => true,
            ],

            [
                'clave' => 'SUF_DIAS_VIGENCIA',
                'valor' => '20',
                'tipo' => 'int',
                'grupo' => 'SUFICIENCIA',
                'descripcion' => 'DIAS DE VIGENCIA PARA SUFICIENCIA PRESUPUESTAL',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'SUF_DIAS_RECORDATORIO',
                'valor' => '5',
                'tipo' => 'int',
                'grupo' => 'SUFICIENCIA',
                'descripcion' => 'TIEMPO DE RECORDATORIO EN VIGENCIA DE SUFICIENCIA PRESUPUESTAL',
                'editable' => true,
                'activo' => true,
            ],

            [
                'clave' => 'SEG_PASSWORD_MIN_LENGTH',
                'valor' => '8',
                'tipo' => 'int',
                'grupo' => 'SEGURIDAD',
                'descripcion' => 'LIMITE DE CARACTERES PARA CONTRASENA',
                'editable' => true,
                'activo' => true,
            ],

            [
                'clave' => 'ARCHIVOS_TAM_MAX_MB',
                'valor' => '8',
                'tipo' => 'int',
                'grupo' => 'ARCHIVOS',
                'descripcion' => 'TAMANO DE ARCHIVO ADJUNTO O ANEXOS (MB)',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'ARCHIVOS_EXT_PERMITIDAS',
                'valor' => '["AVI","GIF","INF","LNK","MP4","PDF"]',
                'tipo' => 'json',
                'grupo' => 'ARCHIVOS',
                'descripcion' => 'EXTENSIONES PERMITIDAS PARA ANEXOS',
                'editable' => true,
                'activo' => true,
            ],

            [
                'clave' => 'SESION_MINUTOS',
                'valor' => '10',
                'tipo' => 'int',
                'grupo' => 'SISTEMA',
                'descripcion' => 'TIEMPO EN MINUTOS DE SESION',
                'editable' => true,
                'activo' => true,
            ],
            [
                'clave' => 'EJERCICIO_FISCAL_ACTIVO',
                'valor' => '2026',
                'tipo' => 'int',
                'grupo' => 'SISTEMA',
                'descripcion' => 'EJERCICIO FISCAL ACTIVO',
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
