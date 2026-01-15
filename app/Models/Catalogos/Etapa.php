<?php

namespace App\Models\Catalogos;

use App\Models\Traits\HasDataTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Etapa extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $table = 'etapas';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    // public function ProcedimientoEtapas() {
    //     return $this->hasMany(ProcedimientoEtapa::class, 'etapa_id');
    // }

    // public function ProcedimientoEtapaResponsables() {
    //     return $this->hasMany(ProcedimientoEtapaResponsable::class, 'etapa_id');
    // }

    // public function DocumentosRequeridos() {
    //     return $this->hasMany(DocumentoRequerido::class, 'etapa_id');
    // }

    // public function ExpedientesPorEtapaActual() {
    //     return $this->hasMany(Expediente::class, 'etapa_actual_id');
    // }

    // public function ExpedienteHistorial() {
    //     return $this->hasMany(ExpedienteHistorial::class, 'etapa_id');
    // }
}
