<?php

namespace App\Models\Operacion;

use App\Models\Catalogos\Dependencia;
use App\Models\Catalogos\EstatusExpediente;
use App\Models\Catalogos\Etapa;
use App\Models\Catalogos\TipoProcedimiento;
use App\Models\Catalogos\UnidadAdministrativa;
use App\Models\Traits\HasDataTable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expediente extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $table = 'expedientes';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    // ========================= RELACIONES =========================

    // public function AnioFiscal() {
    //     return $this->belongsTo(AnioFiscal::class, 'anio_fiscal_id');
    // }

    // public function TipoProcedimiento() {
    //     return $this->belongsTo(TipoProcedimiento::class, 'tipo_procedimiento_id');
    // }

    // public function Dependencia() {
    //     return $this->belongsTo(Dependencia::class, 'dependencia_id');
    // }

    // public function UnidadAdministrativa() {
    //     return $this->belongsTo(UnidadAdministrativa::class, 'unidad_administrativa_id');
    // }

    // public function Estatus() {
    //     return $this->belongsTo(EstatusExpediente::class, 'estatus_id');
    // }

    // public function EtapaActual() {
    //     return $this->belongsTo(Etapa::class, 'etapa_actual_id');
    // }

    public function CreadoPor() {
        return $this->belongsTo(User::class, 'creado_por');
    }

    // public function Historial() {
    //     return $this->hasMany(ExpedienteHistorial::class, 'expediente_id');
    // }

    // public function Documentos() {
    //     return $this->hasMany(ExpedienteDocumento::class, 'expediente_id');
    // }
}
