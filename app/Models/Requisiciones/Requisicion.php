<?php

namespace App\Models\Requisiciones;

use App\Models\Catalogos\Paaas;
use App\Models\Catalogos\UnidadAdministrativa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Requisicion extends Model {
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function Dependencia() {
        return $this->belongsTo(\App\Models\Catalogos\Dependencia::class, 'dependencia_id');
    }

    public function UnidadAdministrativa() {
        return $this->belongsTo(UnidadAdministrativa::class, 'unidad_administrativa_id');
    }

    public function Pass() {
        return $this->belongsTo(Paaas::class, 'pass_id');
    }


    public function Partidas() {
        return $this->hasMany(RequisicionPartida::class, 'requisicion_id');
    }

    public function Requisitos() {
        // return $this->hasOne(RequisicionRequisito::class, 'requisicion_id');
    }

    public function Suficiencia() {
        // return $this->hasOne(RequisicionSuficiencia::class, 'requisicion_id');
    }

    public function ClavesPresupuestales() {
        return $this->hasMany(RequisicionClavePresupuestal::class, 'requisicion_id');
    }

    public function ObjetosGasto() {
        return $this->belongsToMany(
            ObjetoGasto::class,
            'requisiciones_objetos_gastos',
            'requisicion_id',
            'objeto_gasto_id'
        )->withTimestamps();
    }

    public function DocumentosVinculos() {
        // return $this->morphMany(DocumentoVinculo::class, 'vinculable');
    }
}
