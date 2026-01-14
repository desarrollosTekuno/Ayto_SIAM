<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\Dependencia;
use App\Models\Catalogos\DependenciaDato;
use App\Models\Catalogos\DependenciaDireccion;
use App\Models\Catalogos\Estado;
use App\Models\Catalogos\Titular;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;


class DependenciasController extends Controller {

    public function index(Request $request) {
        $Titulares = Titular::Catalogo();
        $Estados = Estado::select('id', 'nombre')->orderBy('nombre')->get();
        $Dependencias = Dependencia::with('Direccion', 'Datos.Titular')->forDataTable($request, defaultPerPage: 10);
        $DependenciasPadre = Dependencia::CatalogoPadre();

        return Inertia::render('Catalogos/Dependencias', compact('Titulares', 'Dependencias', 'Estados', 'DependenciasPadre'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:150'],
            'cveDep' => ['nullable', 'string', 'max:5'],
            'cveURes' => ['nullable', 'string', 'max:4'],
            'abreviatura' => ['nullable', 'string', 'max:100'],
            'tipo' => ['nullable', 'integer'],
            'centralizada' => ['nullable', 'boolean'],
            'usado_en' => ['nullable', 'string', 'max:20'],
            'dependencia_padre_id' => ['nullable', 'integer', 'exists:dependencias,id'],

            'titular_id' => ['nullable', 'integer', 'exists:titulares,id'],
            'telefono' => ['required', 'string', 'max:20'],
            'extension' => ['nullable', 'string', 'max:10'],

            'calle' => ['required', 'string', 'max:150'],
            'numero_exterior' => ['required', 'string', 'max:20'],
            'numero_interior' => ['nullable', 'string', 'max:20'],
            'colonia' => ['required', 'string', 'max:120'],
            'codigo_postal' => ['required', 'string', 'max:10'],
            'estado_id' => ['nullable', 'integer', 'exists:estados,id'],
            'municipio_id' => ['nullable', 'integer', 'exists:municipios,id'],
        ]);

        DB::transaction(function () use ($validated) {
            $dependencia = Dependencia::create([
                'nombre' => $validated['nombre'],
                'cveDep' => $validated['cveDep'] ?? null,
                'cveURes' => $validated['cveURes'] ?? null,
                'abreviatura' => $validated['abreviatura'] ?? null,
                'tipo' => $validated['tipo'] ?? 0,
                'centralizada' => $validated['centralizada'] ?? true,
                'usado_en' => $validated['usado_en'] ?? 'SIAM',
                'dependencia_padre_id' => $validated['dependencia_padre_id'] ?? null,
            ]);

            DependenciaDato::create([
                'dependencia_id' => $dependencia->id,
                'titular_id' => $validated['titular_id'] ?? null,
                'telefono' => $validated['telefono'],
                'extension' => $validated['extension'] ?? null,
            ]);

            DependenciaDireccion::create([
                'dependencia_id' => $dependencia->id,
                'calle' => $validated['calle'],
                'numero_exterior' => $validated['numero_exterior'],
                'numero_interior' => $validated['numero_interior'] ?? null,
                'colonia' => $validated['colonia'],
                'codigo_postal' => $validated['codigo_postal'],
                'estado_id' => $validated['estado_id'] ?? null,
                'municipio_id' => $validated['municipio_id'] ?? null,
            ]);
        });

        return redirect()->route('dependencias.index')->with('success', 'Dependencia registrada correctamente');
    }

    public function update(Request $request, Dependencia $dependencia) {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:150'],
            'cveDep' => ['nullable', 'string', 'max:5'],
            'cveURes' => ['nullable', 'string', 'max:4'],
            'abreviatura' => ['nullable', 'string', 'max:100'],
            'tipo' => ['nullable', 'integer'],
            'centralizada' => ['nullable', 'boolean'],
            'usado_en' => ['nullable', 'string', 'max:20'],
            'dependencia_padre_id' => ['nullable', 'integer', 'exists:dependencias,id'],

            'titular_id' => ['nullable', 'integer', 'exists:titulares,id'],
            'telefono' => ['required', 'string', 'max:20'],
            'extension' => ['nullable', 'string', 'max:10'],

            'calle' => ['required', 'string', 'max:150'],
            'numero_exterior' => ['required', 'string', 'max:20'],
            'numero_interior' => ['nullable', 'string', 'max:20'],
            'colonia' => ['required', 'string', 'max:120'],
            'codigo_postal' => ['required', 'string', 'max:10'],
            'estado_id' => ['nullable', 'integer', 'exists:estados,id'],
            'municipio_id' => ['nullable', 'integer', 'exists:municipios,id'],
        ]);

        DB::transaction(function () use ($validated, $dependencia) {
            $dependencia->update([
                'nombre' => $validated['nombre'],
                'cveDep' => $validated['cveDep'] ?? null,
                'cveURes' => $validated['cveURes'] ?? null,
                'abreviatura' => $validated['abreviatura'] ?? null,
                'tipo' => $validated['tipo'] ?? 0,
                'centralizada' => $validated['centralizada'] ?? true,
                'usado_en' => $validated['usado_en'] ?? 'SIAM',
                'dependencia_padre_id' => $validated['dependencia_padre_id'] ?? null,
            ]);

            $dependencia->Datos()->updateOrCreate(
                ['dependencia_id' => $dependencia->id],
                [
                    'titular_id' => $validated['titular_id'] ?? null,
                    'telefono' => $validated['telefono'],
                    'extension' => $validated['extension'] ?? null,
                ]
            );

            $dependencia->Direccion()->updateOrCreate(
                ['dependencia_id' => $dependencia->id],
                [
                    'calle' => $validated['calle'],
                    'numero_exterior' => $validated['numero_exterior'],
                    'numero_interior' => $validated['numero_interior'] ?? null,
                    'colonia' => $validated['colonia'],
                    'codigo_postal' => $validated['codigo_postal'],
                    'estado_id' => $validated['estado_id'] ?? null,
                    'municipio_id' => $validated['municipio_id'] ?? null,
                ]
            );
        });

        return redirect()->route('dependencias.index')->with('success', 'Dependencia actualizada correctamente');
    }

    public function destroy(Dependencia $dependencia) {
        DB::transaction(function () use ($dependencia) {
            $dependencia->Datos()->delete();
            $dependencia->Direccion()->delete();
            $dependencia->delete();
        });

        return redirect()->route('dependencias.index')->with('success', 'Dependencia eliminada correctamente');
    }
}
