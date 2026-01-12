<?php
// routes/Catalogos.php

use App\Http\Controllers\Catalogos\AreasController;
use App\Http\Controllers\Catalogos\DepartamentosController;
use App\Http\Controllers\Catalogos\DependenciasController;
use App\Http\Controllers\Catalogos\MunicipiosController;
use App\Http\Controllers\Catalogos\TitularesController;
use App\Http\Controllers\CatalogosCargosController;
use Illuminate\Support\Facades\Route;

Route::resource('/cargos', CatalogosCargosController::class)->names('cargos');
Route::resource('/titulares', TitularesController::class)->names('titulares');
Route::resource('/areas', AreasController::class)->names('areas');
Route::resource('/dependencias', DependenciasController::class)->names('dependencias');
Route::resource('/departamentos', DepartamentosController::class)->names('departamentos');

Route::get('/municipios/{estado_id}', [MunicipiosController::class, 'BuscarMunicipio'])->name('estados.municipios');
