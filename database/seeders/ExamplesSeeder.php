<?php

namespace Database\Seeders;

use App\Models\Examples;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamplesSeeder extends Seeder {

    public function run(): void {
        Examples::factory(300)->create();
    }
}
