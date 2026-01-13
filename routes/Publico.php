<?php

use App\Http\Controllers\Catalogos\FormatosLineamientosController;
use Illuminate\Support\Facades\Route;

Route::get('formatos/visualizar', [FormatosLineamientosController::class, 'ListarFormatos'])->name('formatos.visualizar');
