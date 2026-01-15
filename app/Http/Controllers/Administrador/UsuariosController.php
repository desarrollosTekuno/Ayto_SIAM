<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\Cargo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;

class UsuariosController extends Controller {

    public function index(Request $request) {
        $Cargos = Cargo::select('id', 'nombre')->orderBy('nombre')->get();
        $Usuarios = User::with('Dato', 'Titular')->forDataTable($request, defaultPerPage: 10);
        return Inertia::render('Administrador/Usuarios', compact('Usuarios', 'Cargos'));
    }

    public function ReiniciarPassword(Request $request) {
        $data = $request->validate([
            'id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $user = User::findOrFail($data['id']);
        $user->password = Hash::make($user->username);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Contraseña reiniciada correctamente']);
    }

    public function AsignarRoles(Request $request) {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'roles'   => ['nullable', 'array'],
            'roles.*' => ['string'],
        ]);

        $user = User::findOrFail($data['user_id']);

        $roles = $data['roles'] ?? [];

        $user->syncRoles($roles);

        return response()->json([
            'ok' => true,
            'user_id' => $user->id,
            'roles' => $user->getRoleNames(),
        ]);
    }
}
