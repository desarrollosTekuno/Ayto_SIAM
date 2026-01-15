<?php

namespace App\Models\Configuracion;

use App\Models\Catalogos\Cargo;
use App\Models\Catalogos\Etapa;
use App\Models\Catalogos\TipoProcedimiento;
use App\Models\Traits\HasDataTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcedimientoEtapaResponsable extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $table = 'procedimiento_etapa_responsables';

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    // ========================= RELACIONES =========================
    public function TipoProcedimiento() {
        return $this->belongsTo(TipoProcedimiento::class, 'tipo_procedimiento_id');
    }

    public function Etapa() {
        return $this->belongsTo(Etapa::class, 'etapa_id');
    }

    public function Cargo() {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }
}
