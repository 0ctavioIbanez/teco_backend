<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Modelo;

class ModeloController extends Controller
{
    public function get(Request $request) {
        return response(Modelo::get($request));
    }

    public function update(Request $request) {
        return response(Modelo::updateModelo($request));
    }

    public function delete(Request $request) {
        return response(Modelo::remove($request->idModelo));
    }
}
