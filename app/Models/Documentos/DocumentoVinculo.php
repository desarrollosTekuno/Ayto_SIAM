<?php

namespace App\Models\Documentos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentoVinculo extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'documentos_vinculos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function Documento() {
        return $this->belongsTo(Documento::class, 'documento_id');
    }

    public function Vinculable() {
        return $this->morphTo();
    }
}
