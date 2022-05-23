<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Models\Imagen;

class DepartamentoController extends Controller
{
    public function create(Request $request)
    {
      $request->validate([
        'departamento' => 'unique:Departamento'
      ]);

      Departamento::create($request);
    }

    public static function get($id = null)
    {
      return response()->json(array(
        'res' => Departamento::get($id),
      ));
    }

    public function test(Request $request)
    {
      return response()->json([
        "res" => Imagen::thumb($request->image)
      ]);
    }
}
