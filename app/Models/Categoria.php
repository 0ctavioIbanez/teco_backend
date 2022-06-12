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
      if (!$id) {
        return DB::table("Categoria")->get();
      }

      $categoria = DB::table("Categoria")->where('id', $id)->first();
      if ($categoria) {
        $main = DB::table('Imagen')->where('id', $categoria->idImagenMain)->first();
        $categoria->mainImage = $main ? $main->image : null;
        $cover = DB::table('Imagen')->where('id', $categoria->idImagenCover)->first();
        $categoria->coverImage = $cover ? $cover->image : null;
        $categoria->departamentos = DB::table("CategoriaDepartamento AS cd")
          ->join("Departamento AS d", "d.id", "cd.idDepartamento")
          ->where('cd.idCategoria', $categoria->id)->get();
      }
      return $categoria;
    }

    public static function actualiza($request)
    {
      $mainImage = $request->mainImage ? $request->mainImage[0] : null;
      $coverImage = $request->coverImage ? $request->coverImage[0] : null;

      $update = array(
        "categoria" => $request->categoria,
      );

      if ($mainImage) {
        $update["idImagenMain"] = Imagen::upload($mainImage);
      }
      if ($coverImage) {
        $update["idImagenCover"] = Imagen::upload($coverImage);
      }

      DB::table("Categoria")->where('id', $request->id)->update($update);
      DB::table("CategoriaDepartamento")->where('idCategoria', $request->id)->delete();

      foreach ($request->departamentos as $key => $depto) {
        self::createCatDepto($request->id, $depto);
      }
    }

    public static function eraseAll($request)
    {
      DB::table("CategoriaDepartamento")->where('idCategoria', $request->id)->delete();
      DB::table("Categoria")->where('id', $request->id)->delete();
    }

    public static function removeImage($request)
    {
      $update = [];
      if ($request->type == 'cover') {
        $update["idImagenCover"] = null;
      } else {
        $update["idImagenMain"] = null;
      }

      DB::table("Imagen")->where('id', $request->idImage)->delete();
      DB::table("Categoria")->where('id', $request->id)->update($update);
    }
}
