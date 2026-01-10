<?php
// routes/Administrador.php

use App\Http\Controllers\Administrador\UsuariosController;
use Illuminate\Support\Facades\Route;


Route::resource('/usuarios', UsuariosController::class)->names('usuarios');
