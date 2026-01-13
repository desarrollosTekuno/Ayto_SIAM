<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\Cargo;
use App\Models\Catalogos\Titular;
use Illuminate\Http\Request;
use Inertia\Inertia;


class TitularesController extends Controller {

    public function index(Request $request) {
        $Cargos = Cargo::select('id', 'nombre')->orderBy('nombre')->get();
        $Titular = Titular::forDataTable($request, defaultPerPage: 10);
        return Inertia::render('Catalogos/Titulares', compact('Titular', 'Cargos'));
    }

    public function show($id) {
        return Titular::findOrFail($id);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:150'],
            'apellido_paterno' => ['nullable', 'string', 'max:150'],
            'apellido_materno' => ['nullable', 'string', 'max:150'],
            'correo' => ['nullable', 'string', 'email', 'max:95'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'extension' => ['nullable', 'string', 'max:10'],
            'cargo_id' => ['nullable', 'integer', 'exists:cargos,id'],
        ]);

        Titular::create([
            'nombre' => $validated['nombre'],
            'apellido_paterno' => $validated['apellido_paterno'] ?? null,
            'apellido_materno' => $validated['apellido_materno'] ?? null,
            'correo' => $validated['correo'] ?? null,
            'telefono' => $validated['telefono'] ?? null,
            'extension' => $validated['extension'] ?? null,
            'cargo_id' => $validated['cargo_id'] ?? null,
        ]);

        return redirect()->route('titulares.index')->with('success', 'Titular registrado correctamente');
    }

    public function update(Request $request) {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:150'],
            'apellido_paterno' => ['nullable', 'string', 'max:150'],
            'apellido_materno' => ['nullable', 'string', 'max:150'],
            'correo' => ['nullable', 'string', 'email', 'max:95'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'extension' => ['nullable', 'string', 'max:10'],
            'cargo_id' => ['nullable', 'integer', 'exists:cargos,id'],
        ]);

        Titular::where('id', $request->id)->update([
            'nombre' => $validated['nombre'],
            'apellido_paterno' => $validated['apellido_paterno'] ?? null,
            'apellido_materno' => $validated['apellido_materno'] ?? null,
            'correo' => $validated['correo'] ?? null,
            'telefono' => $validated['telefono'] ?? null,
            'extension' => $validated['extension'] ?? null,
            'cargo_id' => $validated['cargo_id'] ?? null,
        ]);

        return redirect()->route('titulares.index')->with('success', 'Titular actualizado correctamente');
    }

    public function destroy(Titular $titular) {
        $titular->delete();
        return redirect()->route('titulares.index')->with('success', 'Titular eliminado correctamente');
    }
}
