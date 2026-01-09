<?php

namespace App\Models\Requisiciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObjetoGasto extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'objetos_gastos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function Requisiciones() {
        return $this->belongsToMany(
            Requisicion::class,
            'requisiciones_objetos_gastos',
            'objeto_gasto_id',
            'requisicion_id'
        )->withTimestamps();
    }
}
