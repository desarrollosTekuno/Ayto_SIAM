<?php

namespace App\Models\Formatos;

use App\Models\Traits\HasDataTable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LineamientoGeneralArchivo extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function ActualizadoPor() {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
