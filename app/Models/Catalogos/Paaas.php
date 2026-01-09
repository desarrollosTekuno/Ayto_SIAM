<?php

namespace App\Models\Catalogos;

use App\Models\Requisiciones\Requisicion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paaas extends Model {
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];


    public function Requisiciones() {
        return $this->belongsToMany(
            Requisicion::class,
            'requisiciones_paaas',
            'paaas_id',
            'requisicion_id'
        )->withTimestamps();
    }
}
