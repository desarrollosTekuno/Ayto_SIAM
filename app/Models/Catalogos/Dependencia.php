<?php

namespace App\Models\Catalogos;

use App\Models\Requisiciones\Requisicion;
use App\Models\Traits\HasDataTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dependencia extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function Requisiciones() {
        return $this->hasMany(Requisicion::class, 'dependencia_id');
    }

    public function Datos() {
        return $this->hasOne(DependenciaDato::class, 'dependencia_id');
    }

    public function Direccion() {
        return $this->hasOne(DependenciaDireccion::class, 'dependencia_id');
    }
}
