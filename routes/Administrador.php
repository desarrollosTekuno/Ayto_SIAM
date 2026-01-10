<?php
// routes/Administrador.php

use App\Http\Controllers\Administrador\UsuariosController;
use Illuminate\Support\Facades\Route;


Route::resource('/Usuarios', UsuariosController::class)->names('Usuarios');
