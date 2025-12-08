<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {

    public function run(): void {
        User::factory()->create([
            'name' => 'Super Administrator',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('1234'),
        ]);

        $this->call([
            ExamplesSeeder::class,
        ]);
    }
}
