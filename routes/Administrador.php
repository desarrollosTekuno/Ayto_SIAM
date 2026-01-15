<?php
// routes/Administrador.php

use App\Http\Controllers\Administrador\RolesPermisosController;
use App\Http\Controllers\Administrador\UsuariosController;
use App\Http\Controllers\Configuracion\ArchivosPermitidosController;
use App\Http\Controllers\Configuracion\ConfiguracionSistemaController;
use App\Http\Controllers\Configuracion\RangosProcedimientosController;
use App\Http\Controllers\Configuracion\TiposProcesosController;
use Illuminate\Support\Facades\Route;


Route::resource('/usuarios', UsuariosController::class)->names('usuarios');
Route::post('/usuarios/AsignarRoles', [UsuariosController::class, 'AsignarRoles'])->name('usuarios.AsignarRoles');
Route::post('/usuarios/ReiniciarPassword', [UsuariosController::class, 'ReiniciarPassword'])->name('usuarios.ReiniciarPassword');

Route::resource('/RolesPermisos', RolesPermisosController::class)->names('RolesPermisos');
Route::post('/RolesPermisos/Roles', [RolesPermisosController::class, 'Roles'])->name('RolesPermisos.Roles');
Route::post('/RolesPermisos/PermisosAsigados', [RolesPermisosController::class, 'PermisosAsigados'])->name('RolesPermisos.PermisosAsigados');
Route::post('/RolesPermisos/AsignarPermisos', [RolesPermisosController::class, 'AsignarPermisos'])->name('RolesPermisos.AsignarPermisos');
Route::post('/RolesPermisos/ConsultarRolesPermisos', [RolesPermisosController::class, 'ConsultarRolesPermisos'])->name('RolesPermisos.ConsultarRolesPermisos');


Route::resource('/configuraciones_sistema', ConfiguracionSistemaController::class)->names('configuraciones_sistema');
Route::resource('/tipos_procesos', TiposProcesosController::class)->names('tipos_procesos');
Route::resource('/rangos_procedimientos', RangosProcedimientosController::class)->names('rangos_procedimientos');
Route::resource('/archivos_permitidos', ArchivosPermitidosController::class)->names('archivos_permitidos');
