<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Banner;

class TiendaController extends Controller
{
    public function getScaffolding()
    {
      return [
        "categories" => Categoria::getScaffolding(),
        "recommended" => [],
        "mostSellers" => [],
        "offers" => [],
        "banners" => Banner::get()
      ];
    }

    public function getProducto($request)
    {
      return Producto::getPublic($request);
    }
}
