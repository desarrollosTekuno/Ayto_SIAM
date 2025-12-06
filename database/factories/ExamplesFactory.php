<?php

namespace Database\Factories;

use App\Models\Examples;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamplesFactory extends Factory {

    protected $model = Examples::class;

    public function definition(): array {
        return [
            'hechizo'   => $this->faker->randomElement([
                'Bola de Fuego',
                'Niebla Arcana',
                'Rayo Crepuscular',
                'Escudo Etéreo',
                'Canto de Sombras'
            ]),
            'ingrediente_principal' => $this->faker->randomElement([
                'Lágrima de salamandra',
                'Polvo de hada',
                'Esencia de luna',
                'Pluma de fénix',
                'Raíz de mandrágora'
            ]),
            'codigo_runico'         => strtoupper($this->faker->bothify('???###')),
            'correo_mago'           => $this->faker->safeEmail(),
            'telefono_mago'         => $this->faker->numerify('22########'),

            'nivel_hechizo'         => $this->faker->numberBetween(1, 9),
            'costo_mana'            => $this->faker->randomFloat(2, 5, 500),

            'password_grimorio'     => bcrypt('secreto'.$this->faker->numberBetween(1,9)),

            'fecha_ritual'          => $this->faker->date(),

            'bestia_favorita_id'    => $this->faker->numberBetween(1, 5),
            'rango_mago'            => $this->faker->randomElement([
                                        'aprendiz', 'hechicero', 'archimago'
                                    ]),

            'acepta_riesgos_magicos'=> $this->faker->boolean(),
            'modo_silencioso'       => $this->faker->boolean(),
            'turno_nocturno'        => $this->faker->boolean(),
            'canal_hechizo'         => $this->faker->randomElement(['etereo', 'fisico']),
            'modo_trabajo'          => $this->faker->randomElement(['normal', 'estricto']),

            'diario_mago'           => $this->faker->sentence(8),

            'pergaminos_path'       => null,
            'documentos_arcanos_path' => null,

            'grimorio_html'         => '<p>'.$this->faker->sentence(12).'</p>',

            'hora_ritual'           => $this->faker->time('H:i'),

            'poder_encantamiento'   => $this->faker->numberBetween(0, 100),
        ];
    }
}
