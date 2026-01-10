<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\Dependencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class DependenciasController extends Controller {

    public function index() {
        $Dependencias = Dependencia::get();
        return Inertia::render('Catalogos/Dependencias', compact('Dependencias'));
    }

    public function MyFunction(Request $request) {
        return "MyFunction";
    }
}
