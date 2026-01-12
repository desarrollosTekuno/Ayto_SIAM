<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estado extends Model {
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function Municipios() {
        return $this->hasMany(Municipio::class, 'estado_id');
    }

    public function Direcciones() {
        return $this->hasMany(DependenciaDireccion::class, 'estado_id');
    }
}
