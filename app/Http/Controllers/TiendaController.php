<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Models\Categoria;
use App\Models\Producto;

class TiendaController extends Controller
{
    public function getScaffolding()
    {
      return [
        "categories" => Categoria::getScaffolding(),
        "recommended" => [],
        "mostSellers" => [],
        "offers" => []
      ];
    }

    public function getProducto($request)
    {
      return Producto::getPublic($request);
    }
}
