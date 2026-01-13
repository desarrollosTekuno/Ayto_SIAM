<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Models\Formatos\LineamientoGeneralArchivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;


class FormatosLineamientosController extends Controller {

    public function index(Request $request) {
        $Formatos = LineamientoGeneralArchivo::forDataTable($request, defaultPerPage: 10);
        return Inertia::render('Formatos/Lineamientos', compact('Formatos'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'titulo' => ['nullable', 'string', 'max:850'],
            'archivo' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:10240'],
        ]);

        $path = $request->file('archivo')->store('FormatosLineamientos', 'public');

        LineamientoGeneralArchivo::create([
            'nombre' => $validated['nombre'],
            'titulo' => $validated['titulo'] ?? null,
            'archivo' => $path,
            'actualizado_por' => $request->user()?->id,
        ]);

        return redirect()->route('lineamientos.index')->with('success', 'Archivo cargado correctamente');
    }

    public function update(Request $request, LineamientoGeneralArchivo $lineamiento) {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'titulo' => ['nullable', 'string', 'max:850'],
            'archivo' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:10240'],
        ]);

        $archivoPath = $lineamiento->getRawOriginal('archivo');

        if ($request->hasFile('archivo')) {
            if ($archivoPath) {
                Storage::disk('public')->delete($archivoPath);
            }

            $archivoPath = $request->file('archivo')->store('FormatosLineamientos', 'public');
        }

        $lineamiento->update([
            'nombre' => $validated['nombre'],
            'titulo' => $validated['titulo'] ?? null,
            'archivo' => $archivoPath,
            'actualizado_por' => $request->user()?->id,
        ]);

        return redirect()->route('lineamientos.index')->with('success', 'Archivo actualizado correctamente');
    }

    public function destroy(LineamientoGeneralArchivo $lineamiento) {
        $archivoPath = $lineamiento->getRawOriginal('archivo');

        if ($archivoPath) {
            Storage::disk('public')->delete($archivoPath);
        }

        $lineamiento->delete();

        return redirect()->route('lineamientos.index')->with('success', 'Archivo eliminado correctamente');
    }

}
