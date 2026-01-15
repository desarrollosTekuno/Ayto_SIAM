<?php

namespace App\Models\Catalogos;

use App\Models\Requisiciones\Requisicion;
use App\Models\Traits\HasDataTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dependencia extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $table = 'dependencias';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function Requisiciones() {
        return $this->hasMany(Requisicion::class, 'dependencia_id');
    }

    public function Titular() {
        return $this->belongsTo(Titular::class, 'titular_id');
    }

    public function Dato() {
        return $this->hasOne(DependenciaDato::class, 'dependencia_id');
    }

    public function Direccion() {
        return $this->hasOne(DependenciaDireccion::class, 'dependencia_id');
    }

    public function DependenciaPadre() {
        return $this->belongsTo(self::class, 'dependencia_padre_id');
    }

    public function DependenciasHijas() {
        return $this->hasMany(self::class, 'dependencia_padre_id');
    }

    public function UnidadesAdministrativas() {
        return $this->hasMany(UnidadAdministrativa::class, 'dependencia_id');
    }



    //  =========================================== CATALOGOS ============================================
    public static function CatalogoPadre() {
        return self::select(
                'id',
                'nombre',
                'cveDep'
            )
            ->orderBy('nombre')
            ->get()
            ->map(function ($dependencia) {
                return [
                    'id' => $dependencia->id,
                    'nombre' => trim(
                        $dependencia->cveDep
                            ? "{$dependencia->cveDep} - {$dependencia->nombre}"
                            : $dependencia->nombre
                    ),
                ];
            });
    }
}
