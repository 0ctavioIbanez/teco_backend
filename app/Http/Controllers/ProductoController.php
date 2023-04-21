<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Modelo;

class ProductoController extends Controller
{
    public function create(Request $request)
    {
      $exists = Producto::validate($request->general["codigo"]);

      if ($exists) {
        return response(["status" => "BAD", "message" => "El código ya ha sido usado"], 420);
      }

      $idProducto = Producto::createGeneral($request->general);
      Producto::createDetalles($request->detalles, $idProducto);
      Producto::createModelos($request->modelos, $idProducto);

      return response([
        "status" => $exists,
        "message" => "Producto creado correctamente",
      ]);
    }

    public function get($id=null)
    {
      return response(Producto::get($id));
    }

    public function search(Request $request)
    {
      return response(Producto::search($request));
    }

    /*
    * Returns search results for search engine
    */
    public function searchTienda(Request $request)
    {
      return response(Producto::searchTienda($request));
    }

    /*
    * Returns all products according params
    */
    public function getTienda(Request $request)
    {
      return response(Producto::getProductDetail($request));
    }

    public function updateColor(Request $request)
    {
      return response(Producto::updateColor($request));
    }


    public function createColor(Request $request)
    {
      return response(Producto::createColor($request));
    }

    public function createModelo(Request $request)
    {
      return response(Modelo::createModelo($request));
    }
}
