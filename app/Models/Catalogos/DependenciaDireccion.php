<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DependenciaDireccion extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'dependencia_direcciones';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function Dependencia() {
        return $this->belongsTo(Dependencia::class, 'dependencia_id');
    }

    public function Estado() {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function Municipio() {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }
}
