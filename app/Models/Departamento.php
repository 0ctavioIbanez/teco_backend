<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Imagen;

class Departamento extends Model
{
    use HasFactory;

    public static function create($request)
    {
      $inicio = Imagen::upload($request->inicio);
      $cover = Imagen::upload($request->cover);

      return DB::table('Departamento')->insertGetId([
        "departamento" => $request->departamento,
        "idImagenMain" => $inicio,
        "idImagenCover" => $cover,
      ]);
    }

    public static function get($id)
    {
      $departamento = DB::table("Departamento");

      if ($id) {
        $depto = $departamento->where("id", $id)->first();
        $mainImage = DB::table("Imagen")->where('id', $depto->idImagenMain)->first();
        $coverImage = DB::table("Imagen")->where('id', $depto->idImagenCover)->first();

        $depto->mainImage = $mainImage ? $mainImage->image : null;
        $depto->coverImage = $coverImage ? $coverImage->image : null;
        return $depto;
      }

      $deptos = $departamento->get();

      foreach ($deptos as $key => $depto) {
        $depto->mainImage = DB::table("Imagen")
            ->where('id', $depto->idImagenMain)->first();
        $depto->secondImage = DB::table("Imagen")
            ->where('id', $depto->idImagenCover)->first();
      }

      return $deptos;
    }

    public static function actualiza($request)
    {
      $data = array("departamento" => $request->departamento);

      if ($request->inicio) {
        $data["idImagenMain"] = Imagen::upload($request->inicio);
      }

      if ($request->cover) {
        $data["idImagenCover"] = Imagen::upload($request->cover);
      }

      DB::table('Departamento')->update($data);
    }

    public static function removeImage($idDepto, $idImage)
    {
      DB::table("Imagen")->where('id', $idImage)->delete();

      $depto = DB::table('Departamento')->where("id", $idDepto);

      $depto->where("idImagenMain", $idImage)->update(["idImagenMain" => null]);
      $depto->where("idImagenCover", $idImage)->update(["idImagenCover" => null]);
    }

    public static function remove($request)
    {
      $departamento = self::get($request->id);
      DB::table("Imagen")->where("id", $departamento->idImagenMain)->delete();
      DB::table("Imagen")->where("id", $departamento->idImagenCover)->delete();
      DB::table("Departamento")->where("id", $request->id)->delete();
    }
}
