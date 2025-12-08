<?php

namespace Database\Factories;

use App\Models\Examples;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamplesFactory extends Factory {

    protected $model = Examples::class;

    public function definition(): array {
        return [
            // TEXT INPUTS
            'nombre_receta' => $this->faker->randomElement([
                'Tacos al Pastor',
                'Lasaña Clásica',
                'Sopa de Tomate Cremosa',
                'Pollo al Limón',
                'Ensalada Mediterránea',
                'Brownies de Chocolate',
            ]),

            'codigo_receta' => strtoupper($this->faker->bothify('##??##')),
            'chef_autor' => $this->faker->name(),

            'correo_contacto'   => $this->faker->optional()->safeEmail(),
            'telefono_contacto' => $this->faker->optional()->numerify('55########'),
            'categoria' => $this->faker->randomElement([
                'postre', 'sopa', 'carne', 'ensalada', 'pasta', 'mariscos',
            ]),


            'cocina_id' => $this->faker->optional()->numberBetween(1, 10),
            'porciones' => $this->faker->numberBetween(1, 9),
            'fecha_publicacion' => $this->faker->optional()->date(),
            'nivel_picante' => $this->faker->numberBetween(0, 100),
            'es_vegetariana'  => $this->faker->boolean(),
            'requiere_horno'  => $this->faker->boolean(),

            'descripcion_breve' => $this->faker->optional()->sentence(12),

            'preparacion_html' => '<ol><li>' . implode('</li><li>', [
                'Preparar los ingredientes.',
                'Calentar la sartén u horno según se requiera.',
                'Mezclar y cocinar a fuego medio.',
                'Servir caliente y disfrutar.',
            ]) . '</li></ol>',

            'foto_principal_path' => $this->faker->optional()->randomElement([
                'recetas/fotos/tacos-al-pastor.jpg',
                'recetas/fotos/lasana-clasica.jpg',
                'recetas/fotos/sopa-tomate.jpg',
                null,
            ]),

            // Guardamos como JSON en el text
            'galeria_imagenes_path' => $this->faker->optional()->randomElement([
                json_encode([
                    'recetas/galeria/img1.jpg',
                    'recetas/galeria/img2.jpg',
                ]),
                null,
            ]),

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

}
