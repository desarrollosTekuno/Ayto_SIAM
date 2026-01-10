<?php

namespace App\Models\Documentos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Documento extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'documentos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function Vinculos() {
        return $this->hasMany(DocumentoVinculo::class, 'documento_id');
    }
}
