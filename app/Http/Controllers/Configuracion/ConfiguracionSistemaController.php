<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Configuracion\ConfiguracionSistema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class ConfiguracionSistemaController extends Controller {

    public function index(Request $request) {
        $Configuraciones = ConfiguracionSistema::forDataTable($request, defaultPerPage: 5);
        return Inertia::render('Configuracion/ConfiguracionSistema', compact('Configuraciones'));
    }

    public function MyFunction(Request $request) {
        return "MyFunction";
    }
}
