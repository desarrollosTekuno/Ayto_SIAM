<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class AreasController extends Controller {

    public function index(Request $request) {
        $Areas = Area::forDataTable($request, defaultPerPage: 5);
        return Inertia::render('Catalogos/Areas', compact('Areas'));
    }

    public function MyFunction(Request $request) {
        return "MyFunction";
    }
}
