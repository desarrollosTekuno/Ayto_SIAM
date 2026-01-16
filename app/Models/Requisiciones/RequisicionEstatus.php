<?php

namespace App\Models\Requisiciones;

use App\Models\Traits\HasDataTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequisicionEstatus extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $table = 'requisiciones_estatus';

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];
}
