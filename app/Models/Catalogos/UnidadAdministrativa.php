<?php

namespace App\Models\Catalogos;

use App\Models\Traits\HasDataTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnidadAdministrativa extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $table = 'unidades_administrativas';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function Dependencia() {
        return $this->belongsTo(Dependencia::class, 'dependencia_id');
    }

    public function Datos() {
        return $this->hasOne(UnidadAdministrativaDato::class, 'unidad_administrativa_id');
    }

    public function Direccion() {
        return $this->hasOne(UnidadAdministrativaDireccion::class, 'unidad_administrativa_id');
    }

    public function UnidadPadre() {
        return $this->belongsTo(self::class, 'unidad_padre_id');
    }

    public function UnidadesHijas() {
        return $this->hasMany(self::class, 'unidad_padre_id');
    }
}
