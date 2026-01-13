<?php

namespace App\Models\Catalogos;

use App\Models\Traits\HasDataTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SecretariaDato extends Model {
    use HasFactory, SoftDeletes, HasDataTable;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function Secretaria() {
        return $this->belongsTo(Secretaria::class, 'secretaria_id');
    }

    public function Titular() {
        return $this->belongsTo(Titular::class, 'titular_id');
    }
}

