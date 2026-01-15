<?php

namespace App\Models\Catalogos;

use App\Models\Configuracion\RangosProcedimiento;
use App\Models\Operacion\Expediente;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnioFiscal extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'años_fiscales';

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];


    public function Expedientes() {
        return $this->hasMany(Expediente::class, 'anio_fiscal_id');
    }

    public function RangosProcedimientos() {
        return $this->hasMany(RangosProcedimiento::class, 'anio_fiscal_id');
    }
}
