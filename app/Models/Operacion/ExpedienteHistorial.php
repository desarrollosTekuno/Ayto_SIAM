<?php

namespace App\Models\Operacion;

use App\Models\Catalogos\EstatusExpediente;
use App\Models\Catalogos\Etapa;
use App\Models\Traits\HasDataTable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpedienteHistorial extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $table = 'expediente_historial';

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    // ========================= RELACIONES =========================

    public function Expediente() {
        return $this->belongsTo(Expediente::class, 'expediente_id');
    }

    public function Etapa() {
        return $this->belongsTo(Etapa::class, 'etapa_id');
    }

    public function Estatus() {
        return $this->belongsTo(EstatusExpediente::class, 'estatus_id');
    }

    public function Usuario() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
