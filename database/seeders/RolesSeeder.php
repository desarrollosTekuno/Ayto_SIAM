<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder {
    public function run(): void
    {
        $roles = [
            'SUPERADMINISTRADOR',
            'ANALISTA',
            'OPERADOR',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role, 'guard_name' => 'web']
            );
        }
    }
}
