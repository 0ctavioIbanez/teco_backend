<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bodega;

class BodegaController extends Controller
{
    public function create(Request $request) {
        return response(Bodega::create($request));
    }

    public function get(Request $request) {
        return response(Bodega::get($request));
    }
}
