<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    public function create(Request $request)
    {
      $this->validate($request, [
        "categoria" => "unique:Categoria"
      ]);

      $id = Categoria::create($request);
      return response([
        "status" => "ok",
        "message" => "Categoría creada correctamente",
        "id" => $id
      ]);
    }

    public function get()
    {
      return Categoria::get();
    }
}
