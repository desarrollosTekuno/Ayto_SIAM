<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfiguracionSistema extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'configuraciones_sistemas';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    // ========================= RELACIONES =========================

    public function Valores() {
        // return $this->hasMany(ConfiguracionSistemaValor::class, 'configuracion_sistema_id');
    }
}
