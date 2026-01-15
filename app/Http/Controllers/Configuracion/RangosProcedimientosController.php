<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Configuracion\RangosProcedimiento;
use App\Models\Configuracion\TiposProceso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class RangosProcedimientosController extends Controller {
    public function index(Request $request) {
        $RangosProcedimientos = RangosProcedimiento::forDataTable($request, defaultPerPage: 10);

        $TiposProcesos = TiposProceso::query()
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Configuracion/RangosProcedimientos', compact('RangosProcedimientos', 'TiposProcesos'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'tipo_proceso_id' => ['required', 'exists:tipos_procesos,id'],
            'clave' => ['required', 'string', 'max:20', 'unique:rangos_procedimientos,clave'],
            'nombre' => ['required', 'string', 'max:150'],
            'monto_min' => ['required', 'numeric', 'min:0'],
            'monto_max' => ['nullable', 'numeric', 'min:0'],
            'activo' => ['required', 'boolean'],
        ]);

        if (!is_null($validated['monto_max']) && $validated['monto_max'] < $validated['monto_min']) {
            return back()->withErrors([
                'monto_max' => 'El monto máximo debe ser mayor o igual al monto mínimo.',
            ]);
        }

        RangosProcedimiento::create($validated);

        return redirect()->back();
    }

    public function update(Request $request, string $id) {
        $item = RangosProcedimiento::findOrFail($id);

        $validated = $request->validate([
            'tipo_proceso_id' => ['required', 'exists:tipos_procesos,id'],
            'clave' => ['required', 'string', 'max:20', 'unique:rangos_procedimientos,clave,' . $item->id],
            'nombre' => ['required', 'string', 'max:150'],
            'monto_min' => ['required', 'numeric', 'min:0'],
            'monto_max' => ['nullable', 'numeric', 'min:0'],
            'activo' => ['required', 'boolean'],
        ]);

        if (!is_null($validated['monto_max']) && $validated['monto_max'] < $validated['monto_min']) {
            return back()->withErrors([
                'monto_max' => 'El monto máximo debe ser mayor o igual al monto mínimo.',
            ]);
        }

        $item->update($validated);

        return redirect()->back();
    }

    public function destroy(string $id) {
        $item = RangosProcedimiento::findOrFail($id);
        $item->delete();

        return redirect()->back();
    }
}
