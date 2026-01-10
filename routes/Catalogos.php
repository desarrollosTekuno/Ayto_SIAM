<?php
// routes/Catalogos.php

use App\Http\Controllers\Catalogos\AreasController;
use App\Http\Controllers\Catalogos\DepartamentosController;
use App\Http\Controllers\Catalogos\DependenciasController;
use Illuminate\Support\Facades\Route;

Route::resource('/areas', AreasController::class)->names('areas');
Route::resource('/dependencias', DependenciasController::class)->names('dependencias');
Route::resource('/departamentos', DepartamentosController::class)->names('departamentos');
