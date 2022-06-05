<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Imagen;

class Categoria extends Model
{
    use HasFactory;

    public static function create($request)
    {

      $mainImage = null;
      $coverImage = null;

      if ($request->mainImage) {
        $mainImage = $request->mainImage[0];
      }

      if ($request->coverImage) {
        $coverImage = $request->coverImage[0];
      }


      $id = DB::table("Categoria")->insertGetId([
        "categoria" => $request->categoria,
        "idImagenMain" => Imagen::upload($mainImage),
        "idImagenCover" => Imagen::upload($coverImage),
      ]);

      foreach ($request->departamentos as $key => $depto) {
        self::createCatDepto($id, $depto);
      }
      return $id;
    }

    private static function createCatDepto($idCat, $idDepto)
    {
      return DB::table("CategoriaDepartamento")->insertGetId([
        "idCategoria" => $idCat,
        "idDepartamento" => $idDepto
      ]);
    }

    public static function get($id='')
    {
      return DB::table("Categoria")->get();
    }
}
