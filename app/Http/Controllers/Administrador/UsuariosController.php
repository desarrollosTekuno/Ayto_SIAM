<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class UsuariosController extends Controller {

    public function index() {
        return Inertia::render('Administrador/Usuarios');
    }

    public function MyFunction(Request $request) {
        return "MyFunction";
    }
}
