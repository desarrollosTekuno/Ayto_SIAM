<?php

namespace App\Models\Catalogos;

use App\Models\Traits\HasDataTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnidadAdministrativaDato extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function UnidadAdministrativa() {
        return $this->belongsTo(UnidadAdministrativa::class, 'unidad_administrativa_id');
    }
}
