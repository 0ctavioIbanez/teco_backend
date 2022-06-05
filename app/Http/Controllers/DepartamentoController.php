<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Models\Imagen;

class DepartamentoController extends Controller
{
    public function create(Request $request)
    {
      $this->validate($request, [
        'departamento' => 'unique:Departamento'
      ]);

      $id = Departamento::create($request);

      return response([
        "status" => "ok",
        "message" => "Departamento creado correctamente",
        "id" => $id,
      ]);
    }

    public function get($id = null)
    {
      return response()->json(array(
        'res' => Departamento::get($id),
      ));
    }

    public function update(Request $request)
    {
      Departamento::actualiza($request);
      return response(["status" => "ok", "message" => "Actualizado correctamente"]);
    }

    public function removeImage(Request $request)
    {
      Departamento::removeImage($request->id, $request->idImage);
      return response(["status" => 'ok', "message" => "Imágen removida"]);
    }

    public function remove(Request $request)
    {
      Departamento::remove($request);
      return response(["status" => "ok", "message" => "Eliminado correctamente"]);
    }

    public function test(Request $request)
    {
      return response()->json([
        "res" => Imagen::thumb($request->image)
      ]);
    }
}
