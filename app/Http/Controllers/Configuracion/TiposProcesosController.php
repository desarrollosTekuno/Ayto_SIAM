<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Configuracion\TiposProceso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class TiposProcesosController extends Controller {

    public function index(Request $request) {
        $TiposProcesos = TiposProceso::forDataTable($request, defaultPerPage: 10);

        return Inertia::render('Configuracion/TiposProcesos', compact('TiposProcesos'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'clave' => ['required', 'string', 'max:10', 'unique:tipos_procesos,clave'],
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'activo' => ['required', 'boolean'],
        ]);

        TiposProceso::create($validated);

        return redirect()->back();
    }

    public function update(Request $request, string $id) {
        $item = TiposProceso::findOrFail($id);

        $validated = $request->validate([
            'clave' => ['required', 'string', 'max:10', 'unique:tipos_procesos,clave,' . $item->id],
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'activo' => ['required', 'boolean'],
        ]);

        $item->update($validated);

        return redirect()->back();
    }

    public function destroy(string $id) {
        $item = TiposProceso::findOrFail($id);
        $item->delete();

        return redirect()->back();
    }
}
