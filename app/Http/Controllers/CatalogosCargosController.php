<?php

namespace App\Http\Controllers;

use App\Models\Catalogos\Cargo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class CatalogosCargosController extends Controller {

    public function index(Request $request) {
        $Cargos = Cargo::forDataTable($request, defaultPerPage: 10);
        return Inertia::render('Catalogos/Cargos', compact('Cargos'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:150'],
        ]);

        Cargo::create([
            'nombre' => $validated['nombre'],
        ]);

        return redirect()->route('cargos.index')->with('success', 'Cargo registrado correctamente');
    }

    public function update(Request $request) {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:150'],
        ]);

        Cargo::where('id', $request->id)->update([
            'nombre' => $validated['nombre'],
        ]);

        return redirect()->route('cargos.index')->with('success', 'Cargo actualizado correctamente');
    }

    public function destroy(Cargo $cargo) {
        $cargo->delete();
        return redirect()->route('cargos.index')->with('success', 'Cargo eliminado correctamente');
    }
}
