<?php

namespace App\Models\Configuracion;

use App\Models\Traits\HasDataTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TiposProceso extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function ControlProcesos() {
        return $this->hasMany(ControlProceso::class, 'tipo_proceso_id');
    }

    public function RangosProcedimientos() {
        return $this->hasMany(RangosProcedimiento::class, 'tipo_proceso_id');
    }

    public function ArchivosPermitidosProcesos() {
        return $this->hasMany(ArchivosPermitidosProceso::class, 'tipo_proceso_id');
    }

    public function ConfiguracionesSistema() {
        return $this->hasMany(ConfiguracionSistema::class, 'tipo_proceso_id');
    }
}
