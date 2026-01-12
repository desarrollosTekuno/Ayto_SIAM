<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\Municipio;
use Illuminate\Http\Request;


class MunicipiosController extends Controller {

    public function BuscarMunicipio(int $estado_id) {
        return Municipio::where('estado_id', $estado_id)
            ->orderBy('nombre')
            ->get();
    }

}
