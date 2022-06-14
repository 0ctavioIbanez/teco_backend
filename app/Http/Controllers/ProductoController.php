<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class ProductoController extends Controller
{
    public function create(Request $request)
    {
      $idProducto = Producto::createGeneral($request->general);
      Producto::createDetalles($request->detalles, $idProducto);
      Producto::createModelos($request->modelos, $idProducto);
      return response([
        "status" => "ok",
        "message" => "Producto creado correctamente"
      ]);
    }
}
