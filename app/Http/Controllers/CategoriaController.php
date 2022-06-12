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

    public function get($id = null)
    {
      return Categoria::get($id);
    }

    public function update(Request $request)
    {
      Categoria::actualiza($request);
      return response([
        "status" => "ok",
        "message" => "Actualizado correctamente"
      ]);
    }


    public function deleteAll(Request $request)
    {
      Categoria::eraseAll($request);
      return response([
        "status" => "ok",
        "message" => "Eliminado correctamente"
      ]);
    }

    public function removeImage(Request $request)
    {
      $test = Categoria::removeImage($request);
      return response([
        "status" => "ok",
        "message" => "Imágen eliminada",
        "debug" => $test
      ]);
    }
}
