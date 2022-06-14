<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Imagen;

class Producto extends Model
{
    use HasFactory;

    public static function createGeneral($request)
    {
      $id = DB::table("Producto")->insertGetId([
        "codigo" => $request->codigo,
        "nombre" => $request->nombre,
        "descripcion" => $request->descripcion,
        "costo" => $request->costo,
        "precio" => $request->precio,
        "visible" => $request->visible, // ***************+
        "nota	" => $request->nota,
      ]);

      foreach ($request->images as $key => $image) {
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
      foreach ($request->categorias as $key => $categoria) {
        DB::table("ProductoCategoria")->insert([
          "idProducto" => $idProducto,
          "idCategoria" => $categoria
        ]);
      }

      // Departamentos
      foreach ($request->departamentos as $key => $departamento) {
        DB::table("ProductoDepartamentos")->insert([
          "idProducto" => $idProducto,
          "idDepartamento" => $departamento
        ]);
      }

      // Tags
      foreach ($request->tags as $key => $tag) {
        DB::table("ProductoTag")->insert([
          "idProducto" => $idProducto,
          "idTag" => $tag
        ]);
      }
    }


    public static function createModelos($request, $idProducto)
    {
      // Tallas
      if (is_array($request->talla)) {
        foreach ($request->talla as $key => $talla) {
          DB::table("ProductoTalla")->insert([
            "idProducto" => $idProducto,
            "idTalla" => $talla
          ]);
        }
      } else {
        DB::table("Talla")->insert(["talla" => $request->talla]);
      }

      // Colores
      if (is_array($request->color)) {
        foreach ($request->color as $key => $color) {
          DB::table("ProductoColor")->insert(["idProducto" => $idProducto, "idColor" => $color]);
        }
      } else {
        DB::table("Color")->insert(["color" => $color]);
      }

      DB::table("Modelos")->insert([
        "idProducto" => $idProducto,
        "stock" => $request->stock, // *********
        "visibleSinStock" => $request->visibleSinStock,
        "visible" => $request->visible,
        "costoExtra" => $request->costoExtra,
        "precioExtra" => $request->precioExtra,
      ]);
    }
}
