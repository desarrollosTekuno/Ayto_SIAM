<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\Dependencia;
use App\Models\Catalogos\Estado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class DependenciasController extends Controller {

    public function index() {
        $Dependencias = Dependencia::get();
        $Estados = Estado::select('id', 'nombre')->orderBy('nombre')->get();
        return Inertia::render('Catalogos/Dependencias', compact('Dependencias', 'Estados'));
    }

    public function MyFunction(Request $request) {
        return "MyFunction";
    }
}
