<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class DemoController extends Controller {

    public function index() {
        return Inertia::render('Demo');
    }

    public function MyFunction(Request $request) {
        return "MyFunction";
    }
}
