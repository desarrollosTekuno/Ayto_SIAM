<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DependenciaDato extends Model {
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function Dependencia() {
        return $this->belongsTo(Dependencia::class, 'dependencia_id');
    }

    public function Titular() {
        return $this->belongsTo(Titular::class, 'titular_id');
    }
}
