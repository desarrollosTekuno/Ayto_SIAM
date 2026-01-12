<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class AreasController extends Controller {

    public function index(Request $request) {
        $Areas = Area::forDataTable($request, defaultPerPage: 5);
        return Inertia::render('Catalogos/Areas', compact('Areas'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'nombre'         => ['required', 'string', 'min:3', 'max:255'],
            'abreviatura'    => ['nullable', 'string', 'max:100'],
            'alias'          => ['nullable', 'string', 'max:100'],
            'usado_en'       => ['nullable', 'string', 'max:20'],
            'ayto_biometricos' => ['nullable', 'boolean'],
        ]);

        Area::create([
            'nombre'          => $validated['nombre'],
            'abreviatura'     => $validated['abreviatura'] ?? null,
            'alias'           => $validated['alias'] ?? null,
            'usado_en'        => $validated['usado_en'] ?? null,
            'ayto_biometricos'=> $validated['ayto_biometricos'] ?? true,
        ]);

        return redirect()->route('areas.index')->with('success', 'Área registrada correctamente');
    }

    public function update(Request $request) {
        $validated = $request->validate([
            'nombre'         => ['required', 'string', 'min:3', 'max:255'],
            'abreviatura'    => ['nullable', 'string', 'max:100'],
            'alias'          => ['nullable', 'string', 'max:100'],
            'usado_en'       => ['nullable', 'string', 'max:20'],
            'ayto_biometricos' => ['nullable', 'boolean'],
        ]);

        Area::where('id', $request->id)->update([
            'nombre'          => $validated['nombre'],
            'abreviatura'     => $validated['abreviatura'] ?? null,
            'alias'           => $validated['alias'] ?? null,
            'usado_en'        => $validated['usado_en'] ?? null,
        ]);

        // $area->update([
        //     'nombre'          => $validated['nombre'],
        //     'abreviatura'     => $validated['abreviatura'] ?? null,
        //     'alias'           => $validated['alias'] ?? null,
        //     'usado_en'        => $validated['usado_en'] ?? null,
        //     'ayto_biometricos'=> $validated['ayto_biometricos'] ?? $area->ayto_biometricos,
        // ]);

        return redirect()->route('areas.index')->with('success', 'Área actualizada correctamente');
    }

    public function destroy(Area $area) {
        $area->delete();
        return redirect()->route('areas.index')->with('success', 'Área eliminada correctamente');
    }
}
