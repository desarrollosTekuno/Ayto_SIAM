<?php

namespace App\Models\Configuracion;

use App\Models\Catalogos\TipoProcedimiento;
=========
use App\Models\Traits\HasDataTable;
>>>>>>>>> Temporary merge branch 2
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RangosProcedimiento extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    // public function AnioFiscal() {
    //     return $this->belongsTo(AnioFiscal::class, 'anio_fiscal_id');
    // }

    public function TipoProcedimiento() {
        return $this->belongsTo(TipoProcedimiento::class, 'tipo_procedimiento_id');
    }
}
