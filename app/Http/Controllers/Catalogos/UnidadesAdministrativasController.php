<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\Dependencia;
use App\Models\Catalogos\Estado;
use App\Models\Catalogos\Titular;
use App\Models\Catalogos\UnidadAdministrativa;
use App\Models\Catalogos\UnidadAdministrativaDato;
use App\Models\Catalogos\UnidadAdministrativaDireccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;


class UnidadesAdministrativasController extends Controller {

    public function index(Request $request) {
        $UnidadesAdministrativas = UnidadAdministrativa::with('Dato', 'Direccion.Estado', 'Direccion.Municipio', 'Titular')->forDataTable($request, defaultPerPage: 10);

        $UnidadesPadre = UnidadAdministrativa::CatalogoPadre();
        $Dependencias = Dependencia::select('id', 'nombre', 'cveDep')
            ->orderBy('nombre')
            ->get()
            ->map(function ($dependencia) {
                return [
                    'id' => $dependencia->id,
                    'nombre' => $dependencia->cveDep
                        ? "{$dependencia->cveDep} - {$dependencia->nombre}"
                        : $dependencia->nombre,
                ];
            });
        $Titulares = Titular::Catalogo();
        $Estados = Estado::select('id', 'nombre')->orderBy('nombre')->get();

        return Inertia::render('Catalogos/UnidadesAdministrativas', compact('UnidadesAdministrativas', 'UnidadesPadre', 'Dependencias', 'Titulares', 'Estados'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:150'],
            'siglas' => ['nullable', 'string', 'max:50'],
            'abreviatura' => ['nullable', 'string', 'max:100'],
            'alias' => ['nullable', 'string', 'max:100'],
            'tipo' => ['nullable', 'integer'],
            'unidad_padre_id' => ['nullable', 'integer', 'exists:unidades_administrativas,id'],
            'dependencia_id' => ['required', 'integer', 'exists:dependencias,id'],

            'titular_id' => ['nullable', 'integer', 'exists:titulares,id'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'extension' => ['nullable', 'string', 'max:10'],

            'calle' => ['nullable', 'string', 'max:150'],
            'numero_exterior' => ['nullable', 'string', 'max:20'],
            'numero_interior' => ['nullable', 'string', 'max:20'],
            'colonia' => ['nullable', 'string', 'max:120'],
            'codigo_postal' => ['nullable', 'string', 'max:10'],
            'estado_id' => ['nullable', 'integer', 'exists:estados,id'],
            'municipio_id' => ['nullable', 'integer', 'exists:municipios,id'],
        ]);

        DB::transaction(function () use ($validated) {
            $unidadAdministrativa = UnidadAdministrativa::create([
                'nombre' => $validated['nombre'],
                'siglas' => $validated['siglas'] ?? null,
                'abreviatura' => $validated['abreviatura'] ?? null,
                'alias' => $validated['alias'] ?? null,
                'tipo' => $validated['tipo'] ?? 0,
                'unidad_padre_id' => $validated['unidad_padre_id'] ?? null,
                'dependencia_id' => $validated['dependencia_id'],
                'titular_id' => $validated['titular_id'] ?? null,
            ]);

            UnidadAdministrativaDato::create([
                'unidad_administrativa_id' => $unidadAdministrativa->id,
                'telefono' => $validated['telefono'] ?? null,
                'extension' => $validated['extension'] ?? null,
            ]);

            UnidadAdministrativaDireccion::create([
                'unidad_administrativa_id' => $unidadAdministrativa->id,
                'calle' => $validated['calle'] ?? null,
                'numero_exterior' => $validated['numero_exterior'] ?? null,
                'numero_interior' => $validated['numero_interior'] ?? null,
                'colonia' => $validated['colonia'] ?? null,
                'codigo_postal' => $validated['codigo_postal'] ?? null,
                'estado_id' => $validated['estado_id'] ?? null,
                'municipio_id' => $validated['municipio_id'] ?? null,
            ]);
        });

        return redirect()->route('unidades_administrativas.index')->with('success', 'Unidad administrativa registrada correctamente');
    }

    public function update(Request $request, UnidadAdministrativa $unidades_administrativa) {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:150'],
            'siglas' => ['nullable', 'string', 'max:50'],
            'abreviatura' => ['nullable', 'string', 'max:100'],
            'alias' => ['nullable', 'string', 'max:100'],
            'tipo' => ['nullable', 'integer'],
            'unidad_padre_id' => ['nullable', 'integer', 'exists:unidades_administrativas,id'],
            'dependencia_id' => ['required', 'integer', 'exists:dependencias,id'],

            'titular_id' => ['nullable', 'integer', 'exists:titulares,id'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'extension' => ['nullable', 'string', 'max:10'],

            'calle' => ['nullable', 'string', 'max:150'],
            'numero_exterior' => ['nullable', 'string', 'max:20'],
            'numero_interior' => ['nullable', 'string', 'max:20'],
            'colonia' => ['nullable', 'string', 'max:120'],
            'codigo_postal' => ['nullable', 'string', 'max:10'],
            'estado_id' => ['nullable', 'integer', 'exists:estados,id'],
            'municipio_id' => ['nullable', 'integer', 'exists:municipios,id'],
        ]);

        DB::transaction(function () use ($validated, $unidades_administrativa) {
            $unidades_administrativa->update([
                'nombre' => $validated['nombre'],
                'siglas' => $validated['siglas'] ?? null,
                'abreviatura' => $validated['abreviatura'] ?? null,
                'alias' => $validated['alias'] ?? null,
                'tipo' => $validated['tipo'] ?? 0,
                'unidad_padre_id' => $validated['unidad_padre_id'] ?? null,
                'dependencia_id' => $validated['dependencia_id'],
                'titular_id' => $validated['titular_id'] ?? null,
            ]);

            $unidades_administrativa->Dato()->updateOrCreate(
                ['unidad_administrativa_id' => $unidades_administrativa->id],
                [
                    'telefono' => $validated['telefono'] ?? null,
                    'extension' => $validated['extension'] ?? null,
                ]
            );

            $unidades_administrativa->Direccion()->updateOrCreate(
                ['unidad_administrativa_id' => $unidades_administrativa->id],
                [
                    'calle' => $validated['calle'] ?? null,
                    'numero_exterior' => $validated['numero_exterior'] ?? null,
                    'numero_interior' => $validated['numero_interior'] ?? null,
                    'colonia' => $validated['colonia'] ?? null,
                    'codigo_postal' => $validated['codigo_postal'] ?? null,
                    'estado_id' => $validated['estado_id'] ?? null,
                    'municipio_id' => $validated['municipio_id'] ?? null,
                ]
            );
        });

        return redirect()->route('unidades_administrativas.index')->with('success', 'Unidad administrativa actualizada correctamente');
    }

    public function destroy(UnidadAdministrativa $unidades_administrativa) {
        DB::transaction(function () use ($unidades_administrativa) {
            $unidades_administrativa->Dato()->delete();
            $unidades_administrativa->Direccion()->delete();
            $unidades_administrativa->delete();
        });

        return redirect()->route('unidades_administrativas.index')->with('success', 'Unidad administrativa eliminada correctamente');
    }
}
