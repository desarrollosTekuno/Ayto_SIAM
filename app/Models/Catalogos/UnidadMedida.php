<?php

namespace App\Models\Catalogos;

use App\Models\Requisiciones\RequisicionPartida;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnidadMedida extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'unidades_medidas';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function RequisicionesPartidas() {
        return $this->hasMany(RequisicionPartida::class, 'unidad_medida_id');
    }
}
