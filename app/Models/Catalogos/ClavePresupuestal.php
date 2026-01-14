<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClavePresupuestal extends Model {
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    protected $table = 'claves_presupuestales';

    // public function Requisiciones() {
    //     return $this->belongsToMany(
    //         Requisicion::class,
    //         'requisiciones_claves_presupuestales',
    //         'clave_presupuestal_id',
    //         'requisicion_id'
    //     )->withTimestamps();
    // }
}
