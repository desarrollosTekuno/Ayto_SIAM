<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class UsuariosController extends Controller {

    public function index(Request $request) {

        $Usuarios = User::with('Dato', 'Titular')->forDataTable($request, defaultPerPage: 10);
        return Inertia::render('Administrador/Usuarios', compact('Usuarios'));
    }

    public function MyFunction(Request $request) {
        return "MyFunction";
    }
}
