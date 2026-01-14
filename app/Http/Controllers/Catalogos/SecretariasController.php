<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\Estado;
use App\Models\Catalogos\Secretaria;
use App\Models\Catalogos\SecretariaDato;
use App\Models\Catalogos\SecretariaDireccion;
use App\Models\Catalogos\Titular;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;


class SecretariasController extends Controller {

    public function index(Request $request) {
        $Secretarias = Secretaria::with('Dato.Titular', 'Direccion.Estado', 'Direccion.Municipio')->forDataTable($request, defaultPerPage: 10);

        $SecretariasPadre = Secretaria::CatalogoPadre();
        $Titulares = Titular::Catalogo();
        $Estados = Estado::select('id', 'nombre')->orderBy('nombre')->get();

        return Inertia::render('Catalogos/Secretarias', compact('Secretarias', 'SecretariasPadre', 'Titulares', 'Estados'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:150'],
            'cveDep' => ['nullable', 'string', 'max:5'],
            'cveURes' => ['nullable', 'string', 'max:4'],
            'abreviatura' => ['nullable', 'string', 'max:100'],
            'tipo' => ['nullable', 'integer'],
            'usado_en' => ['nullable', 'string', 'max:20'],
            'secretaria_padre_id' => ['nullable', 'integer', 'exists:secretarias,id'],

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
            $secretaria = Secretaria::create([
                'nombre' => $validated['nombre'],
                'cveDep' => $validated['cveDep'] ?? null,
                'cveURes' => $validated['cveURes'] ?? null,
                'abreviatura' => $validated['abreviatura'] ?? null,
                'tipo' => $validated['tipo'] ?? 0,
                'usado_en' => $validated['usado_en'] ?? 'SIAM',
                'secretaria_padre_id' => $validated['secretaria_padre_id'] ?? null,
            ]);

            SecretariaDato::create([
                'secretaria_id' => $secretaria->id,
                'titular_id' => $validated['titular_id'] ?? null,
                'telefono' => $validated['telefono'],
                'extension' => $validated['extension'] ?? null,
            ]);

            SecretariaDireccion::create([
                'secretaria_id' => $secretaria->id,
                'calle' => $validated['calle'],
                'numero_exterior' => $validated['numero_exterior'],
                'numero_interior' => $validated['numero_interior'] ?? null,
                'colonia' => $validated['colonia'],
                'codigo_postal' => $validated['codigo_postal'],
                'estado_id' => $validated['estado_id'] ?? null,
                'municipio_id' => $validated['municipio_id'] ?? null,
            ]);
        });

        return redirect()->route('secretarias.index')->with('success', 'Secretaria registrada correctamente');
    }

    public function update(Request $request, Secretaria $secretaria) {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:150'],
            'cveDep' => ['nullable', 'string', 'max:5'],
            'cveURes' => ['nullable', 'string', 'max:4'],
            'abreviatura' => ['nullable', 'string', 'max:100'],
            'tipo' => ['nullable', 'integer'],
            'usado_en' => ['nullable', 'string', 'max:20'],
            'secretaria_padre_id' => ['nullable', 'integer', 'exists:secretarias,id'],

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

        DB::transaction(function () use ($validated, $secretaria) {
            $secretaria->update([
                'nombre' => $validated['nombre'],
                'cveDep' => $validated['cveDep'] ?? null,
                'cveURes' => $validated['cveURes'] ?? null,
                'abreviatura' => $validated['abreviatura'] ?? null,
                'tipo' => $validated['tipo'] ?? 0,
                'usado_en' => $validated['usado_en'] ?? 'SIAM',
                'secretaria_padre_id' => $validated['secretaria_padre_id'] ?? null,
            ]);

            $secretaria->Dato()->updateOrCreate(
                ['secretaria_id' => $secretaria->id],
                [
                    'titular_id' => $validated['titular_id'] ?? null,
                    'telefono' => $validated['telefono'],
                    'extension' => $validated['extension'] ?? null,
                ]
            );

            $secretaria->Direccion()->updateOrCreate(
                ['secretaria_id' => $secretaria->id],
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

        return redirect()->route('secretarias.index')->with('success', 'Secretaria actualizada correctamente');
    }

    public function destroy(Secretaria $secretaria) {
        DB::transaction(function () use ($secretaria) {
            $secretaria->Dato()->delete();
            $secretaria->Direccion()->delete();
            $secretaria->delete();
        });

        return redirect()->route('secretarias.index')->with('success', 'Secretaria eliminada correctamente');
    }
}
