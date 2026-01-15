<?php

namespace App\Models\Catalogos;

use App\Models\Traits\HasDataTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoDocumento extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $table = 'tipos_documentos';

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    // ========================= RELACIONES =========================

    // public function DocumentosRequeridos() {
    //     return $this->hasMany(DocumentoRequerido::class,'tipo_documento_id');
    // }

    // public function ExpedienteDocumentos() {
    //     return $this->hasMany(ExpedienteDocumento::class, 'tipo_documento_id');
    // }
}
