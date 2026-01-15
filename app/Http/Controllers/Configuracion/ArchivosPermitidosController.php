<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\TipoProcedimiento;
use App\Models\Configuracion\ArchivoPermitido;
use App\Models\Configuracion\TiposProceso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class ArchivosPermitidosController extends Controller {

    public function index(Request $request) {
        $ArchivosPermitidos = ArchivoPermitido::with('TipoProcedimiento')->forDataTable($request, defaultPerPage: 10);

        $TiposProcedimientos = TipoProcedimiento::query()
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Configuracion/ArchivosPermitidos', compact('ArchivosPermitidos', 'TiposProcedimientos'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'tipo_proceso_id' => ['required', 'exists:tipos_procesos,id'],
            'nombre' => ['required', 'string', 'max:150'],
            'extensiones' => ['required', 'string', 'max:150'],
            'tamano_max_mb' => ['nullable', 'numeric', 'min:0'],
            'obligatorio' => ['required', 'boolean'],
            'activo' => ['required', 'boolean'],
        ]);


        $validated['extensiones'] = collect(explode(',', $validated['extensiones']))
            ->map(fn($x) => strtolower(trim(str_replace('.', '', $x))))
            ->filter()
            ->unique()
            ->values()
            ->implode(',');

        ArchivoPermitido::create($validated);

        return redirect()->back();
    }

    public function ModificarEstatus(Request $request) {
        $item = ArchivoPermitido::findOrFail($request->id);
        $item->update(['activo' => !$item->activo]);
        return redirect()->back();
    }

    public function update(Request $request, string $id) {
        $item = ArchivoPermitido::findOrFail($id);

        $validated = $request->validate([
            'tipo_proceso_id' => ['required', 'exists:tipos_procesos,id'],
            'nombre' => ['required', 'string', 'max:150'],
            'extensiones' => ['required', 'string', 'max:150'],
            'tamano_max_mb' => ['nullable', 'numeric', 'min:0'],
            'obligatorio' => ['required', 'boolean'],
            'activo' => ['required', 'boolean'],
        ]);

        $validated['extensiones'] = collect(explode(',', $validated['extensiones']))
            ->map(fn($x) => strtolower(trim(str_replace('.', '', $x))))
            ->filter()
            ->unique()
            ->values()
            ->implode(',');

        $item->update($validated);

        return redirect()->back();
    }

    public function destroy(string $id) {
        $item = ArchivoPermitido::findOrFail($id);
        $item->delete();

        return redirect()->back();
    }
}
