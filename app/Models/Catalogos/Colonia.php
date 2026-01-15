<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Colonia extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'colonias';

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function Municipio() {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    public function CodigosPostales() {
        return $this->hasMany(CodigoPostal::class, 'colonia_id');
    }
}
