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

        $mainImage = $request->image;
        // $coverImage = $request->coverImage;

      $id = DB::table("Categoria")->insertGetId([
        "categoria" => $request->categoria,
        "idImagenMain" => Imagen::upload($mainImage),
        // "idImagenCover" => Imagen::upload($coverImage),
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
      $mainImage = $request->image;
      // $coverImage = $request->coverImage;

      $update = array(
        "categoria" => $request->categoria,
      );

      if ($mainImage) {
        $update["idImagenMain"] = Imagen::upload($mainImage);
      }
      // if ($coverImage) {
      //   $update["idImagenCover"] = Imagen::upload($coverImage);
      // }

      DB::table("Categoria")->where('id', $request->id)->update($update);
      DB::table("CategoriaDepartamento")->where('idCategoria', $request->id)->delete();

      foreach ($request->departamentos as $key => $depto) {
        self::createCatDepto($request->id, $depto);
      }
    }

    public static function eraseAll($request)
    {
      DB::table("Imagen")->where("id", $request->idImagenMain)->delete();
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

    public static function getCategoriaDepartamento()
    {
      $deptos = DB::table("Departamento")->get();
      foreach ($deptos as $key => $depto) {
        $categorias = DB::table("CategoriaDepartamento as cd")
        ->join("Categoria as c", "cd.idCategoria", "c.id")
        ->where("cd.idDepartamento", $depto->id)
        ->get();

        foreach ($categorias as $key => $categoria) {
          $categoria->image = DB::table("Imagen")->where("id", $categoria->idImagenMain)->select("image")->first()?->image;
        }

        $depto->categorias = $categorias;
      }
      return $deptos;
    }

    public static function getScaffolding()
    {
      $sections = DB::table('Departamento as D')
      ->select("D.id", "departamento", "image")
      ->leftJoin("Imagen as I", "I.id", "D.idImagenMain")
      ->get();

      foreach ($sections as $key => $section) {
        $section->categories = DB::table("Categoria as C")
                              ->select("C.id", "categoria", "image")
                              ->leftJoin("Imagen as I", "C.idImagenMain", "I.id")
                              ->get();
      }

      return $sections;
    }
}
