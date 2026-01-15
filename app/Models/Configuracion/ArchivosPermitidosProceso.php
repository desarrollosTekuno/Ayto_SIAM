<?php

namespace App\Models\Configuracion;

use App\Models\Traits\HasDataTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ArchivosPermitidosProceso extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $table = 'archivos_permitidos_procesos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function TiposProceso() {
        return $this->belongsTo(TiposProceso::class, 'tipo_proceso_id');
    }
}
