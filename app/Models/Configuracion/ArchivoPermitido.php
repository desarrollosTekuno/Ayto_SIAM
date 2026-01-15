<?php

namespace App\Models\Configuracion;

use App\Models\Catalogos\TipoProcedimiento;
use App\Models\Traits\HasDataTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArchivoPermitido extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $table = 'archivos_permitidos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function TipoProcedimiento() {
        return $this->belongsTo(TipoProcedimiento::class);
    }
}
