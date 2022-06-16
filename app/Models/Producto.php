<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Imagen;

class Producto extends Model
{
    use HasFactory;

    public static function validate($code)
    {
      return DB::table("Producto")->where('codigo', $code)->first() || false;
    }

    public static function createGeneral($request)
    {
      $id = DB::table("Producto")->insertGetId([
        "codigo" => $request["codigo"],
        "nombre" => $request["nombre"],
        "descripcion" => $request["descripcion"],
        "costo" => $request["costo"],
        "precio" => $request["precio"],
        "visible" => $request["visible"],
        "nota" => $request["nota"],
      ]);

      foreach ($request["images"] as $key => $image) {
        DB::table("ProductoImagen")->insert([
          "idProducto" => $id,
          "idImagen" => Imagen::upload($image)
        ]);
      }
      return $id;
    }

    public static function createDetalles($request, $idProducto)
    {
      // Categorias
      foreach ($request["categorias"] as $key => $categoria) {
        DB::table("ProductoCategoria")->insert([
          "idProducto" => $idProducto,
          "idCategoria" => $categoria
        ]);
      }

      // Departamentos
      foreach ($request["departamentos"] as $key => $departamento) {
        DB::table("ProductoDepartamento")->insert([
          "idProducto" => $idProducto,
          "idDepartamento" => $departamento
        ]);
      }

      // Tags
      foreach ($request["tags"] as $key => $tag) {
        DB::table("ProductoTag")->insert([
          "idProducto" => $idProducto,
          "idTag" => $tag
        ]);
      }

      // Codigos
      foreach ($request["codigos"] as $key => $codigo) {
        DB::table("ProductoCodigo")->insert([
          "idProducto" => $idProducto,
          "codigo" => $codigo
        ]);
      }
    }


    public static function createModelos($request, $idProducto)
    {
      // Tallas
      if (is_array($request["talla"])) {
        foreach ($request["talla"] as $key => $talla) {
          DB::table("ProductoTalla")->insert([
            "idProducto" => $idProducto,
            "idTalla" => $talla
          ]);
        }
      } else {
        $idTalla = DB::table("Talla")->insertGetId(["talla" => ucfirst($request["talla"])]);
        DB::table("ProductoTalla")->insert([
          "idProducto" => $idProducto,
          "idTalla" => $idTalla
        ]);
      }

      // Colores
      if (is_array($request["color"])) {
        foreach ($request["color"] as $key => $color) {
          DB::table("ProductoColor")->insert(["idProducto" => $idProducto, "idColor" => $color]);
        }
      } else {
        $idColor = DB::table("Color")->insert(["color" => $color]);
        DB::table("ProductoColor")->insert(["idProducto" => $idProducto, "idColor" => $idColor]);
      }

      DB::table("Modelos")->insert([
        "idProducto" => $idProducto,
        "stock" => $request["stock"],
        "visibleSinStock" => $request["visibleSinStock"],
        "visible" => $request["visible"],
        "costoExtra" => $request["costoExtra"],
        "precioExtra" => $request["precioExtra"],
      ]);
    }
}
