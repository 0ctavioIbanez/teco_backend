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
}
