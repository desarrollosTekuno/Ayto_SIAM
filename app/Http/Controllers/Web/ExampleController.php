<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Examples;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class ExampleController extends Controller {

    public function index(Request $request) {
        $items = Examples::forDataTable($request, defaultPerPage: 5);
        return Inertia::render('Examples/Example', compact('items'));
    }

    public function create() {
        return Inertia::render('Examples/Create', [
            'selects' => $this->getSelectData()
        ]);
    }

    public function store(Request $request) {
        $data = $this->validateData($request);

        Examples::create($data);

        return redirect()->route('examples.index')
            ->with('success', 'Hechizo registrado.');
    }

    public function edit($id) {
        $item = Examples::findOrFail($id);

        return Inertia::render('Examples/Edit', [
            'item'    => $item,
            'selects' => $this->getSelectData(),
        ]);
    }

    public function update(Request $request, $id) {
        $data = $this->validateData($request);

        $item = Examples::findOrFail($id);
        $item->update($data);

        return redirect()->route('examples.index')
            ->with('success', 'Hechizo actualizado.');
    }

    public function destroy($id) {
        $item = Examples::findOrFail($id);
        $item->delete();

        return back()->with('success', 'Registro enviado al limbo.');
    }

    public function restore($id) {
        $item = Examples::withTrashed()->findOrFail($id);
        $item->restore();

        return back()->with('success', 'Registro devuelto del más allá.');
    }

    private function validateData(Request $request) {
        return $request->validate([
            'hechizo'               => 'required|string|max:80',
            'ingrediente_principal' => 'required|string|max:100',
            'codigo_runico'         => 'required|string|max:12',
            'correo_mago'           => 'required|email|max:150',
            'telefono_mago'         => 'nullable|string|max:20',

            'nivel_hechizo'         => 'required|integer|min:1|max:9',
            'costo_mana'            => 'required|numeric|min:0',

            'password_grimorio'     => 'nullable|string|max:100',

            'fecha_ritual'          => 'nullable|date',

            'bestia_favorita_id'    => 'nullable|integer|min:1|max:5', // Simulación
            'rango_mago'            => 'nullable|string|max:30',

            'acepta_riesgos_magicos'=> 'boolean',
            'modo_silencioso'       => 'boolean',
            'turno_nocturno'        => 'boolean',
            'canal_hechizo'         => 'string|max:20',
            'modo_trabajo'          => 'string|max:20',

            'diario_mago'           => 'nullable|string',

            'pergaminos_path'       => 'nullable|string|max:255',
            'documentos_arcanos_path' => 'nullable|string',

            'grimorio_html'         => 'nullable|string',

            'hora_ritual'           => 'nullable',

            'poder_encantamiento'   => 'nullable|integer|min:0|max:100',
        ]);
    }

    private function getSelectData() {
        return [
            'bestias' => [
                ['id' => 1, 'nombre' => 'Fénix'],
                ['id' => 2, 'nombre' => 'Dragón Esmeralda'],
                ['id' => 3, 'nombre' => 'Grifo Dorado'],
                ['id' => 4, 'nombre' => 'Serpiente Alada'],
                ['id' => 5, 'nombre' => 'Tortuga Celestial'],
            ],
            'rangos' => [
                'aprendiz',
                'hechicero',
                'archimago'
            ],
            'canales' => [
                'etereo',
                'fisico'
            ],
            'modos' => [
                'normal',
                'estricto'
            ]
        ];
    }

}
