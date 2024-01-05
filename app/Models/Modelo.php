<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Nette\Utils\Arrays;

class Modelo extends Model
{
  use HasFactory;

  public static function createModelo($request)
  {
    $idModelo = DB::table("Modelos")->insertGetId([
      "idProducto" => $request->id,
      "stock" => $request->stock,
      "visibleSinStock" => $request->visibleSinStock,
      "visible" => $request->visible,
      "costoExtra" => $request->costoExtra,
      "precioExtra" => $request->precioExtra,
      "idTalla" => $request->talla
    ]);

    return $idModelo;
  }

  public static function updateModelo($request)
  {
    DB::table("Modelos")
    ->where("id", $request->idModelo)
    ->update([
      "idProducto" => $request->idProducto,
      "stock" => $request->stock,
      "visibleSinStock" => $request->visibleSinStock,
      "visible" => $request->visible,
      "costoExtra" => $request->costoExtra,
      "precioExtra" => $request->precioExtra,
      "idTalla" => $request->idTalla
    ]);

    return ["message" => "Modelo actualizado correctamente", "payload" => $request->idModelo];
  }

  public static function deleteImage($request)
  {
    DB::table("Imagen")->where("id", $request->idImage)->delete();
    DB::table("ModelosImagen")->where("idImagen", $request->idModeloImagen)->delete();
    return ["message" => "Imágen eliminada correctamente"];
  }

  public static function get($request)
  {
    $modelo = DB::table("Modelos AS m")
      ->select(
        "codigo",
        "costo",
        "costoExtra",
        "descripcion",
        "idProducto",
        "idTalla",
        "nombre",
        "nota",
        "precio",
        "precioExtra",
        "stock",
        "talla",
        "m.visible",
        "m.visibleSinStock",
        "m.id AS idModelo"
      )


      ->join("Producto AS p", "p.id", "m.idProducto")
      ->join("Talla AS T", "T.id", "m.idTalla");

    if ($request->idProducto) {
      $modelos = $modelo->where("m.idProducto", $request->idProducto)->get();

      foreach ($modelos as $key => $modelo) {
        $modelo->images = DB::table("Imagen as I")
          ->join("ModelosImagen as MI", "MI.idImagen", "I.id")
          ->where("MI.idModelo", $modelo->idModelo)
          ->get();

        $modelo->colors = DB::table('ModeloColor AS MC')
        ->select("color", "hex", "MC.id")
        ->join("Color as C", "C.id", "MC.idColor")
        ->where("MC.idModelo", $modelo->idModelo)
        ->get();

        $modelo->places = DB::table('Bodega AS B')
        ->join("BodegaCelda as BC", "BC.idBodega", "B.id")
        ->join("ProductoCelda AS PC", "PC.idCelda", "BC.idCelda")
        ->join("Celda AS C", "C.id", "PC.idCelda")
        ->select("PC.id AS idPC", "bodega", "B.descripcion as bodega_descripcion", "C.descripcion AS celda_descripcion", "celda", "cantidad", "B.id as idBodega")
        ->where("PC.idModelo", $modelo->idModelo)->get();
      }

      return $modelos;
    }

    return $modelo->get();
  }

  public static function uploadImage($request)
  {
    $modelId = $request->idModel;

    foreach ($request->images as $image) {
      $idImage = Imagen::upload($image);
      DB::table('ModelosImagen')->insert([
        "idModelo" => $modelId,
        "idImagen" => $idImage
      ]);
    }
  }

  public static function remove($idModelo)
  {
    DB::table("ProductoCelda")->where("idModelo", $idModelo)->delete();
    DB::table("ModelosImagen")->where("idModelo", $idModelo)->delete();
    DB::table("Modelos")->where("id", $idModelo)->delete();
    return ["message" => "Eliminado correctamente"];
  }

  public static function createColor($request) {
    DB::table('ModeloColor')->insert([
      "idModelo" => $request->modelId,
      "idColor" => $request->colorId,
    ]);
    return ["message" => "Color creado correctamente"];
  }

  public static function deleteColor($request) {
    DB::table("ModeloColor")->where("id", $request->idModelColor)->delete();
    return ["message" => "Color removido del modelo correctamente"];
  }
}
