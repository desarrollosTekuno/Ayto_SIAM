<?php

namespace App\Models\Requisiciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequisicionSuficiencia extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'requisiciones_suficiencias';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function Requisicion() {
        return $this->belongsTo(Requisicion::class, 'requisicion_id');
    }
}
