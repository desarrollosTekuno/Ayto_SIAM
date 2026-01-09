<?php

namespace App\Models\Requisiciones;

use App\Models\Catalogos\UnidadMedida;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequisicionPartida extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'requisiciones_partidas';

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function Requisicion() {
        return $this->belongsTo(Requisicion::class, 'requisicion_id');
    }

    public function UnidadMedida() {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id');
    }

}
