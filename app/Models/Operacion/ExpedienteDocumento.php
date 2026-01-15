<?php

namespace App\Models\Operacion;

use App\Models\Catalogos\TipoDocumento;
use App\Models\Traits\HasDataTable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpedienteDocumento extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $table = 'expediente_documentos';

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function Expediente() {
        return $this->belongsTo(Expediente::class, 'expediente_id');
    }

    public function TipoDocumento() {
        return $this->belongsTo(TipoDocumento::class, 'tipo_documento_id');
    }

    public function SubidoPor() {
        return $this->belongsTo(User::class, 'subido_por');
    }
}
