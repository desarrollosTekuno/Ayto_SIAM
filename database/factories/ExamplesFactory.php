<?php

namespace Database\Factories;

use App\Models\Examples;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamplesFactory extends Factory {

    protected $model = Examples::class;

    public function definition(): array {
        return [

            // TEXT INPUTS
            'nombre_receta'   => $this->faker->randomElement([
                'Tacos al Pastor',
                'Lasaña Clásica',
                'Sopa de Tomate Cremosa',
                'Pollo al Limón',
                'Ensalada Mediterránea',
                'Brownies de Chocolate',
            ]),

            'codigo_receta'   => strtoupper($this->faker->bothify('REC-###??')),
            'chef_autor'      => $this->faker->name(),

            // EMAIL + PHONE
            'correo_contacto'   => $this->faker->optional()->safeEmail(),
            'telefono_contacto' => $this->faker->optional()->numerify('55########'),

            // CATEGORÍAS
            'categoria'      => $this->faker->randomElement([
                'postre', 'sopa', 'carne', 'ensalada', 'pasta', 'mariscos'
            ]),

            'cocina_id'      => $this->faker->optional()->numberBetween(1, 5), // depende de tu catálogo real

            // NUMBERS
            'porciones'             => $this->faker->numberBetween(1, 10),
            'nivel_dificultad'      => $this->faker->numberBetween(1, 5),
            'tiempo_preparacion_min'=> $this->faker->numberBetween(5, 120),

            // DATES / TIMES
            'fecha_publicacion'     => $this->faker->optional()->date(),
            'hora_sugerida_servicio'=> $this->faker->optional()->time('H:i'),

            // SLIDER
            'nivel_picante'         => $this->faker->numberBetween(0, 100),

            // BOOLEANOS
            'es_vegetariana'        => $this->faker->boolean(),
            'es_vegana'             => $this->faker->boolean(),
            'requiere_horno'        => $this->faker->boolean(),

            // TEXTAREA / RICH TEXT
            'descripcion_breve'     => $this->faker->optional()->sentence(8),

            'ingredientes_html'     => '<ul><li>' . implode('</li><li>', [
                $this->faker->randomElement(['Tomate', 'Cebolla', 'Ajo', 'Pimienta', 'Mantequilla']),
                $this->faker->randomElement(['Harina', 'Azúcar', 'Sal', 'Aceite de Oliva', 'Queso']),
                $this->faker->randomElement(['Pollo', 'Carne Molida', 'Pasta', 'Leche', 'Chocolate']),
            ]) . '</li></ul>',

            'preparacion_html'      => '<ol><li>' . implode('</li><li>', [
                'Preparar los ingredientes.',
                'Calentar la sartén.',
                'Mezclar y cocinar a fuego medio.',
                'Servir caliente.',
            ]) . '</li></ol>',

            'tips_extra'            => $this->faker->optional()->sentence(10),

            // FILES
            'foto_principal_path'   => null,
            'galeria_imagenes_path' => null,

            'created_at'            => now(),
            'updated_at'            => now(),
        ];
    }

}
