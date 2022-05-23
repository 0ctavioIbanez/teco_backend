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
      DB::table('Departamento')->insert([
        "departamento" => $request->name,
        "idImagenMain" => Imagen::upload($request->image),
      ]);
    }

    public static function get($id)
    {
      $deptos = DB::table("Departamentos")->get();

      foreach ($deptos as $key => $depto) {
        $depto->mainImage = DB::table("Imagen")
            ->where('id', $depto->idImagenMain)->first();
        $depto->secondImage = DB::table("Imagen")
            ->where('id', $depto->idImagenCover)->first();
      }

      return $deptos;
    }
}
