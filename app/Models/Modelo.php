<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
      "talla" => $request->talla
    ]);
    
    DB::table("ProductoCelda")->insert([
      "idCelda" => $request->celda,
      "idProducto" => $request->id,
      "idModelo" => $idModelo,
      "cantidad" => $request->cantidad
    ]);

    return ["message" => "Modelo creado correctamente"];
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
        "talla" => $request->talla
      ]);
    
    DB::table("ProductoCelda")
      ->where("idProducto", $request->idProducto)
      ->where("idModelo", $request->idModelo)
      ->delete();
    
    DB::table("ProductoCelda")->insert([
      "idCelda" => $request->celda,
      "idProducto" => $request->idProducto,
      "idModelo" => $request->idModelo,
      "cantidad" => $request->cantidad
    ]);

    return ["message" => "Modelo actualizado correctamente"];
  }

  public static function deleteImage($request)
  {
    DB::table("Imagen")->where("id", $request->idImage)->delete();
    DB::table("ModelosImagen")->where("idImagen", $request->idImage)->delete();
    return ["message" => "Imágen eliminada correctamente"];
  }

  public static function get($request)
  {
    $modelo = DB::table("Modelos AS m")
      ->join("Producto AS p", "p.id", "m.idProducto")
      ->join("ProductoColor AS pc", "pc.idModelo", "m.id")
      ->join("Color as c", "c.id", "pc.idColor")
      ->leftJoin("ProductoTalla AS pt", "pt.idProducto", "p.id")
      ->leftJoin("Talla as t", "t.id", "pt.idTalla");

    if ($request->idProducto) {
      return $modelo->where("m.idProducto", $request->idProducto)->get();
    }

    return $modelo->get();
  }

  public static function remove($idModelo) {
    DB::table("ProductoCelda")->where("idModelo", $idModelo)->delete();
    DB::table("ModelosImagen")->where("idModelo", $idModelo)->delete();
    DB::table("Modelos")->where("id", $idModelo)->delete();
    return ["message" => "Eliminado correctamente"];
  }
}
