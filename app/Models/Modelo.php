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
      return DB::table("Modelos")->insert([
        "idProducto" => $request->id,
        "stock" => $request->stock,
        "visibleSinStock" => $request->visibleSinStock,
        "visible" => $request->visible,
        "costoExtra" => $request->costoExtra,
        "precioExtra" => $request->precioExtra
      ]);
    }

    public static function deleteImage($request)
    {
      DB::table("Imagen")->where("id", $request->idImage)->delete();
      DB::table("ModelosImagen")->where("idImagen", $request->idImage)->delete();
      return ["message" => "Imágen eliminada correctamente"];
    }

    public static function get($request) {
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
}
