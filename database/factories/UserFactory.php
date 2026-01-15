<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Usuarios\UserDato;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

use Illuminate\Support\Carbon;

class UserFactory extends Factory {
    protected $model = User::class;

    public function definition(): array {

        $nombre = $this->faker->firstName();
        $apellidoPaterno = $this->faker->lastName();
        $apellidoMaterno = $this->faker->optional()->lastName();

        return [
            'nombre' => $nombre,
            'apellido_paterno' => $apellidoPaterno,
            'apellido_materno' => $apellidoMaterno,

            'name' => trim(
                $nombre . ' ' .
                $apellidoPaterno . ' ' .
                ($apellidoMaterno ?? '')
            ),

            'username' => null,
            'email' => $this->faker->optional()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => null,

            'remember_token' => Str::random(10),
        ];
    }

    public function configure() {
        return $this->afterCreating(function (User $user) {

            $prefix = now()->format('Y');
            $folio  = str_pad((string) $user->id, 4, '0', STR_PAD_LEFT);

            $user->username = "{$prefix}{$folio}";
            $user->password = Hash::make($user->username);
            $user->save();

            UserDato::create([
                'cargo' => $this->faker->jobTitle(),
                'telefono' => $this->faker->optional()->phoneNumber(),
                'extension' => $this->faker->optional()->numerify('###'),
                'activo' => true,
                'unidad_administrativa_id' => 1,

                'user_id' => $user->id,
            ]);

            $role = Role::firstOrCreate(
                ['name' => 'SUPERADMINISTRADOR'],
                ['guard_name' => 'web']
            );

            if (!$user->hasRole($role->name)) {
                $user->assignRole($role);
            }
        });
    }
}
