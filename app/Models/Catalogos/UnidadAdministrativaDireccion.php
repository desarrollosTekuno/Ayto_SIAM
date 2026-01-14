<?php

namespace App\Models\Catalogos;

use App\Models\Traits\HasDataTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnidadAdministrativaDireccion extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $table = 'unidad_administrativa_direcciones';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function UnidadAdministrativa() {
        return $this->belongsTo(UnidadAdministrativa::class, 'unidad_administrativa_id');
    }

    public function Estado() {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function Municipio() {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }
}
