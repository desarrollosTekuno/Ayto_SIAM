<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Usuarios\UserDato;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

use Illuminate\Support\Carbon;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'username' => null,
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => null,
            'remember_token' => Str::random(10),
        ];
    }

    public function configure() {
        return $this->afterCreating(function (User $user) {
            $prefix = Carbon::now()->format('Y');
            $user->username = $prefix . str_pad((string)$user->id, 2, '0', STR_PAD_LEFT);
            $user->password = Hash::make($user->username);
            $user->save();

            UserDato::create([
                'nombre' => $this->faker->firstName(),
                'apellido_paterno' => $this->faker->lastName(),
                'apellido_materno' => $this->faker->optional()->lastName(),
                'unidad_administrativa_id' => 1,
                'dependencia_id' => 1,
                'user_id' => $user->id,
            ]);

            $role = Role::firstOrCreate(
                ['name' => 'SUPERADMINISTRADOR', 'guard_name' => 'web']
            );

            $user->assignRole($role);
        });
    }
}
