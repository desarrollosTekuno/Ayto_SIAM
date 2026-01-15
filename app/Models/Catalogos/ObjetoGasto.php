<?php

namespace App\Models\Catalogos;

use App\Models\Traits\HasDataTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObjetoGasto extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $table = 'objetos_gastos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];
}
