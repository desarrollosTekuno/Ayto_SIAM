<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Examples;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class ExampleController extends Controller {

    public function index(Request $request) {
        $Examples = Examples::forDataTable($request, defaultPerPage: 5);

        $Categorias = [
            [ 'id' => 1, 'nombre' => 'Repostería' ],
            [ 'id' => 2, 'nombre' => 'Carnes' ],
            [ 'id' => 3, 'nombre' => 'Sopas' ],
            [ 'id' => 4, 'nombre' => 'Ensaladas' ],
            [ 'id' => 5, 'nombre' => 'Pastas' ],
            [ 'id' => 6, 'nombre' => 'Mariscos' ],
            [ 'id' => 7, 'nombre' => 'Bebidas' ],
        ];

        $Cocinas = [
            [ 'id' => 1, 'nombre' => 'Cocina Mexicana Tradicional' ],
            [ 'id' => 2, 'nombre' => 'Cocina Italiana' ],
            [ 'id' => 3, 'nombre' => 'Cocina Japonesa' ],
            [ 'id' => 4, 'nombre' => 'Cocina Francesa' ],
            [ 'id' => 5, 'nombre' => 'Cocina Española' ],
            [ 'id' => 6, 'nombre' => 'Cocina China' ],
            [ 'id' => 7, 'nombre' => 'Cocina Mediterránea' ],
            [ 'id' => 8, 'nombre' => 'Cocina Árabe' ],
            [ 'id' => 9, 'nombre' => 'Cocina Vegana' ],
            [ 'id' => 10, 'nombre' => 'Cocina Fusión Moderna' ],
        ];

        return Inertia::render('Examples/Example', compact('Examples', 'Categorias', 'Cocinas'));
    }

    public function store(Request $request) {
        return $data = $this->validateData($request);

        Examples::create($data);

        return redirect()->back();
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

    private function validateData(Request $request) {
        return $request->validate([
            'nombre_receta' => [
                'required',
                'string',
                'min:3',
                'max:150',
            ],

            'codigo_receta' => [
                'required',
                'string',
                'min:6',
                'max:6',
                'regex:/^[0-9]{2}[A-Za-z]{2}[0-9]{2}$/',
            ],

            'chef_autor' => [
                'required',
                'string',
                'min:3',
                'max:120',
            ],

            'correo_contacto' => [
                'required',
                'email',
                'max:150',
            ],

            'telefono_contacto' => [
                'required',
                'string',
                'max:25',
            ],

            'categoria' => [
                'required',
                'string',
                'max:50',
            ],

            'cocina_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'porciones' => [
                'required',
                'integer',
                'min:1',
                'max:9',
            ],

            'fecha_publicacion' => [
                'required',
                'date',
            ],

            'nivel_picante' => [
                'required',
                'integer',
                'min:0',
                'max:100',
            ],

            'es_vegetariana' => [
                'required',
                'boolean',
            ],

            'requiere_horno' => [
                'nullable',
                'boolean',
            ],

            'descripcion_breve' => [
                'required',
                'string',
                'min:10',
                'max:120',
            ],

            'preparacion_html' => [
                'required',
                'string',
                'min:10',
                'max:300',
            ],

            'foto_principal_path' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'galeria_imagenes_path' => [
                'required',
                'array',
            ],

            'galeria_imagenes_path.*' => [
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:5048',
            ],
        ]);
    }


}
