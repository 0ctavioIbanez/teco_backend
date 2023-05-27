<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Modelo;

class ModeloController extends Controller
{
    public function get(Request $request) {
        return response(Modelo::get($request));
    }
}
