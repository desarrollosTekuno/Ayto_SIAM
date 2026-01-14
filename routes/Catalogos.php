<?php
// routes/Catalogos.php

use App\Http\Controllers\Catalogos\AreasController;
use App\Http\Controllers\Catalogos\DepartamentosController;
use App\Http\Controllers\Catalogos\DependenciasController;
use App\Http\Controllers\Catalogos\FormatosLineamientosController;
use App\Http\Controllers\Catalogos\MunicipiosController;
use App\Http\Controllers\Catalogos\SecretariasController;
use App\Http\Controllers\Catalogos\TitularesController;
use App\Http\Controllers\Catalogos\UnidadesAdministrativas;
use App\Http\Controllers\CatalogosCargosController;
use Illuminate\Support\Facades\Route;

Route::resource('/cargos', CatalogosCargosController::class)->names('cargos');
Route::resource('/titulares', TitularesController::class)->names('titulares');
Route::resource('/secretarias', SecretariasController::class)->names('secretarias');
Route::resource('/dependencias', DependenciasController::class)->names('dependencias');
Route::resource('/unidades_administrativas', UnidadesAdministrativas::class)->names('unidades_administrativas');
Route::resource('/lineamientos', FormatosLineamientosController::class)->names('lineamientos');

Route::resource('/areas', AreasController::class)->names('areas');
Route::resource('/departamentos', DepartamentosController::class)->names('departamentos');

Route::get('/municipios/{estado_id}', [MunicipiosController::class, 'BuscarMunicipio'])->name('estados.municipios');
