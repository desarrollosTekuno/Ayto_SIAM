<?php

namespace App\Models\Requisiciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequisicionObjetoGasto extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'requisiciones_objetos_gastos';

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];


    public function Requisiciones() {
        return $this->belongsTo(Requisicion::class, 'requisicion_id');
    }

    public function ObjetosGastos() {
        return $this->belongsTo(ObjetoGasto::class, 'objeto_gasto_id');
    }
}
