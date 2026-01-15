<?php

namespace App\Models\Usuarios;

use App\Models\Catalogos\UnidadAdministrativa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDato extends Model {
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function User() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function UnidadAdministrativa() {
        return $this->belongsTo(UnidadAdministrativa::class, 'unidad_administrativa_id');
    }
}
