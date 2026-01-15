<?php

namespace App\Models\Catalogos;

use App\Models\Configuracion\DocumentoRequerido;
use App\Models\Configuracion\ProcedimientoEtapa;
use App\Models\Configuracion\ProcedimientoEtapaResponsable;
use App\Models\Operacion\Expediente;
use App\Models\Traits\HasDataTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoProcedimiento extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $table = 'tipos_procedimientos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function Expedientes() {
        return $this->hasMany(Expediente::class, 'tipo_procedimiento_id');
    }

    public function ProcedimientoEtapas() {
        return $this->hasMany(ProcedimientoEtapa::class, 'tipo_procedimiento_id');
    }

    public function ProcedimientoEtapaResponsables() {
        return $this->hasMany(ProcedimientoEtapaResponsable::class, 'tipo_procedimiento_id');
    }

    public function DocumentosRequeridos() {
        return $this->hasMany(DocumentoRequerido::class, 'tipo_procedimiento_id');
    }

    // public function UmbralesModalidad() {
    //     return $this->hasMany(UmbralModalidad::class, 'tipo_procedimiento_id');
    // }
}
