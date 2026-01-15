<?php

namespace App\Models\Catalogos;

use App\Models\Traits\HasDataTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstatusExpediente extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $table = 'estatus_expedientes';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    // public function Expedientes() {
    //     return $this->hasMany(Expediente::class, 'estatus_id');
    // }

    // public function ExpedienteHistorial() {
    //     return $this->hasMany(ExpedienteHistorial::class, 'estatus_id');
    // }
}
