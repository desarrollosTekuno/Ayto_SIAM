<?php

namespace App\Models\Catalogos;

use App\Models\Traits\HasDataTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Secretaria extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function Secretaria() {
        return $this->belongsTo(self::class, 'secretaria_padre_id');
    }

    public function SecretariasDependencias() {
        return $this->hasMany(self::class, 'secretaria_padre_id');
    }

    public function Dato() {
        return $this->hasOne(SecretariaDato::class, 'secretaria_id');
    }

    public function Direccion() {
        return $this->hasOne(SecretariaDireccion::class, 'secretaria_id');
    }

    public function SubDirecciones() {
        return $this->hasMany(Direccion::class, 'secretaria_id');
    }


    //  =========================================== Aceesos Directos ============================================
    public static function CatalogoPadre() {
        return self::select(
                'id',
                'nombre',
                'cveDep'
            )
            ->orderBy('nombre')
            ->where('tipo', 0)
            ->get()
            ->map(function ($titular) {
                return [
                    'id' => $titular->id,
                    'nombre' => trim(
                        "{$titular->cveDep} - {$titular->nombre}"
                    ),
                ];
            });
    }
}
