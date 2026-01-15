<?php

namespace Database\Seeders;

use App\Models\Catalogos\AnioFiscal;
use Illuminate\Database\Seeder;

class AnioFiscalSeeder extends Seeder {

public function run(): void {
        $anioActual = (int) now()->format('Y');
        $anios = [$anioActual - 1, $anioActual, $anioActual + 1];

        AnioFiscal::query()->update(['activo' => false]);

        foreach ($anios as $a) {
            AnioFiscal::updateOrCreate(
                ['año' => $a],
                ['activo' => ($a === $anioActual)]
            );
        }
    }
}
