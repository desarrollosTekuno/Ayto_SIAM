<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\TipoProcedimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class TiposProcedimientosController extends Controller {

    public function index (Request $request) {

        $TiposProcedimientos = TipoProcedimiento::forDataTable($request, defaultPerPage: 10);

        return Inertia::render('Configuracion/TiposProcedimientos', compact('TiposProcedimientos'));
    }

}
