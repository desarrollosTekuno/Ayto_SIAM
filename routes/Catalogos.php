<?php
// routes/Catalogos.php

use App\Http\Controllers\Catalogos\DependenciasController;
use Illuminate\Support\Facades\Route;

Route::resource('/dependencias', DependenciasController::class)->names('dependencias');
