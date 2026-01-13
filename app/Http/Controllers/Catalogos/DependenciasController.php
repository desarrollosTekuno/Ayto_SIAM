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
        // $search = trim((string) $request->get('search', ''));

        // $query = Dependencia::query()
        //     ->leftJoin('dependencia_datos as dd', function ($join) {
        //         $join->on('dd.dependencia_id', '=', 'dependencias.id')
        //             ->whereNull('dd.deleted_at');
        //     })
        //     ->leftJoin('dependencia_direccions as dir', function ($join) {
        //         $join->on('dir.dependencia_id', '=', 'dependencias.id')
        //             ->whereNull('dir.deleted_at');
        //     })
        //     ->select([
        //         'dependencias.id',
        //         'dependencias.nombre',
        //         'dependencias.cveDep',
        //         'dependencias.cveURes',
        //         'dependencias.abreviatura',
        //         'dependencias.usado_en',
        //         'dd.nombre_titular',
        //         'dd.cargo_titular',
        //         'dd.telefono',
        //         'dd.extension',
        //         'dir.calle',
        //         'dir.numero_exterior',
        //         'dir.numero_interior',
        //         'dir.colonia',
        //         'dir.codigo_postal',
        //         'dir.estado_id',
        //         'dir.municipio_id',
        //     ]);

        // if ($search !== '') {
        //     $query->where(function ($q) use ($search) {
        //         $q->where('dependencias.nombre', 'like', '%' . $search . '%')
        //             ->orWhere('dependencias.cveDep', 'like', '%' . $search . '%')
        //             ->orWhere('dependencias.abreviatura', 'like', '%' . $search . '%')
        //             ->orWhere('dd.nombre_titular', 'like', '%' . $search . '%')
        //             ->orWhere('dd.telefono', 'like', '%' . $search . '%');
        //     });
        // }

        // $sortMap = [
        //     'id' => 'dependencias.id',
        //     'nombre' => 'dependencias.nombre',
        //     'cveDep' => 'dependencias.cveDep',
        //     'abreviatura' => 'dependencias.abreviatura',
        //     'nombre_titular' => 'dd.nombre_titular',
        //     'telefono' => 'dd.telefono',
        // ];

        // $sortBy = $request->get('sort_by');
        // $sortDir = strtolower($request->get('sort_dir', 'asc'));
        // if (! in_array($sortDir, ['asc', 'desc'], true)) {
        //     $sortDir = 'asc';
        // }

        // if ($sortBy && isset($sortMap[$sortBy])) {
        //     $query->orderBy($sortMap[$sortBy], $sortDir);
        // } else {
        //     $query->orderBy('dependencias.id', 'asc');
        // }

        // $perPage = $request->integer('per_page', 10);
        // $Dependencias = $query->paginate($perPage)->withQueryString();
        return Inertia::render('Catalogos/Dependencias', compact('Titulares', 'Dependencias', 'Estados'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:150'],
            'cveDep' => ['nullable', 'string', 'max:5'],
            'cveURes' => ['nullable', 'string', 'max:4'],
            'abreviatura' => ['nullable', 'string', 'max:100'],
            'usado_en' => ['nullable', 'string', 'max:20'],

            'titular_id' => ['nullable'],
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
                'usado_en' => $validated['usado_en'] ?? 'SIAM',
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
            'usado_en' => ['nullable', 'string', 'max:20'],

            'titular_id' => ['nullable'],
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
                'usado_en' => $validated['usado_en'] ?? 'SIAM',
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
