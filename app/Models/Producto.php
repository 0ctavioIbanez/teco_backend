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

      $idModelo = DB::table("Modelos")->insertGetId([
        "idProducto" => $idProducto,
        "stock" => $request["stock"],
        "visibleSinStock" => $request["visibleSinStock"],
        "visible" => $request["visible"],
        "costoExtra" => $request["costoExtra"],
        "precioExtra" => $request["precioExtra"],
      ]);

      // Imágenes
      foreach ($request['images'] as $key => $image) {
        DB::table("ModelosImagen")->insert([
          "idModelo" => $idModelo,
          "idImagen" => Imagen::upload($image)
        ]);
      }
    }

    public static function get($id)
    {
      $producto = DB::table("Producto AS p")->select("*","p.id AS id");

      if (!$id) {
        $results = $producto
        ->leftJoin("ProductoImagen AS pi", "p.id", "pi.idProducto")
        ->get();

        foreach ($results as $key => $result) {
          $image = DB::table("Imagen")->where("id", $result->idImagen)->first();
          $modelos = DB::table("Modelos AS m")->select("stock")->where("m.idProducto", $result->id)->get();

          $stock = 0;
          foreach ($modelos as $key => $modelo) {
            $stock += $modelo->stock;
          }
          $result->stock = $stock;
          if ($image) {
            $result->thumb = Imagen::thumb($image->image);
          }
        }
        return $results;
      }

      $producto = $producto->where('id', $id)->first();
      $producto->departamentos = DB::table("ProductoDepartamento AS pd")->select("d.*", "i.image")->join('Departamento AS d', 'd.id', 'pd.idDepartamento')
                                  ->leftJoin("Imagen as i", "i.id", "d.idImagenMain")->where('pd.idProducto', $id)->get();

      $producto->categorias = DB::table("ProductoCategoria as pc")->select("c.*", "i.image")->join("Categoria as c", 'c.id', 'pc.idCategoria')
                              ->leftJoin('Imagen as i', "i.id", "c.idImagenMain")->where('pc.idProducto', $id)->get();

      $producto->tags = DB::table("ProductoTag as pt")->select("t.*")->join("Tag as t", 't.id', 'pt.idTag')->where('pt.idProducto', $id)->get();
      $producto->tallas = DB::table("ProductoTalla as pt")->select("t.*")->join("Talla as t", "t.id", "pt.idTalla")->where('pt.idProducto', $id)->get();
      $producto->colores = DB::table("ProductoColor as pc")->select("c.*")->join('Color as c', 'c.id', 'pc.idColor')->where('pc.idProducto', $id)->get();
      $producto->imagenes = DB::table("ProductoImagen as pi")->select("i.image")->join("Imagen as i", "i.id", "pi.idImagen")->where('pi.idProducto', $id)->get();
      $producto->modelos = DB::table("Modelos as m")->select("m.*", "i.image")->leftJoin("ModelosImagen as mi", "m.id", "mi.idModelo")
                          ->leftJoin("Imagen as i", "i.id", "mi.idImagen")->where("m.idProducto", $id)->get();
      return ["details" => $producto];
    }

    /*
    * @params(Object)
    - page
    - search
    - color
    - talla
    - depto
    - categoria
    */
    public static function search($request)
    {
      $page = $request->page;
      $limit = 20;
      $response = [];
      $temps = [];

      $result = DB::table("Producto as p")
      ->select("p.nombre", "p.precio", "p.costo", "p.descripcion", "p.codigo", "p.id as id","pc.idColor", "c.color", "pt.idTalla", "t.talla", "pd.idDepartamento", "pca.idCategoria")
      // Color
      ->leftJoin("ProductoColor as pc", "p.id", "pc.idProducto")
      ->leftJoin("Color as c", "c.id", "pc.idColor")
      // Tallas
      ->leftJoin("ProductoTalla AS pt", "pt.idProducto", "p.id")
      ->leftJoin("Talla as t", "t.id", "pt.idTalla")
      // Deptos
      ->leftJoin("ProductoDepartamento as pd", "pd.idProducto", "pd.id")
      // Categorias
      ->leftJoin("ProductoCategoria as pca", "pca.idProducto", "p.id");

      // Keyword
      if ($request->search) {
        $result->where("p.nombre", "LIKE", "%$request->search%");
      }

      // Color
      if ($request->color) {
        $result->where("c.id", "%$request->color%");
      }

      // Tallas
      if ($request->talla) {
        $result->where("idTalla", $request->talla);
      }
      //departamentos
      if ($request->depto) {
        $result->where("pd.idDepartamento", $request->depto);
      }

      // Categorías
      if ($request->categoria) {
        $result->where("pca.idCategoria", $request->categoria);
      }

      $total = $result->count();
      $offset = ($page * $limit) - $limit;
      $pages = ceil($total / $limit);
      $result = $result
                ->limit($limit)
                ->offset($offset)
                ->get();

      foreach ($result as $key => $res) {
        $exists = in_array($res->id, $temps);

        if (!$exists) {
          $response[] = $res;
          array_push($temps, $res->id);
        }
      }

      // Images and Stock
      foreach ($response as $key => $res) {
        $main = DB::table("ProductoImagen as pi")
                ->join("Imagen as i", "i.id", "pi.idImagen")->where('pi.idProducto', $res->id)
                ->first();
        if (count((Array)$main) > 0) {
          $res->thumb = $main->image;
        } else {
          $second = DB::table("Modelos as m")->join("ModelosImagen as mi", "m.id", "mi.idModelo")
                    ->join("Imagen as i", "i.id", "mi.idImagen")->where('m.idProducto', $res->id)->first();
          if (count((Array)$second) > 0) {
            $res->thumb = $second->image;
          }
        }

        $stock = DB::table("Modelos as m")->select("stock")->where("m.idProducto", $res->id)->first();
        if (count((Array)$stock) > 0) {
          $res->stock = $stock->stock;
        }
      }

      return array(
        'total' => $total,
        'pages' => $pages,
        'items' => $response,
      );
    }

    /*
    * Returns public results
      - d => departamento
      - c => categoria
    */
    public static function searchTienda($request)
    {
      $result = [];

      // Products
      $products = DB::table("Producto AS P")->select("P.id", "nombre", "departamento")
      ->join('ProductoDepartamento AS PD', 'PD.idProducto', 'P.id')
      ->join('Departamento AS D', 'D.id', 'PD.idDepartamento')
      ->where('visible', true)->where('nombre', 'LIKE', "%$request->kw%")->get();
      if (count($products->toArray()) > 0) {
        array_push($result, array('title' => 'Productos', 'items' => $products));
      }

      // Tags
      $tags = DB::table("Tag")->where('tag', 'LIKE', "%$request->kw%")->get();
      if (count($tags->toArray()) > 0) {
        $mapedTag = array_map(fn($item) => ["id" => $item->id, "nombre" => $item->tag], $tags->toArray());
        array_push($result, array('title' => 'Etiquetas', 'items' => $mapedTag));
      }

      // color
      $colors = DB::table('Color')->where('color', 'LIKE', "%$request->kw%")->get();
      if (count($colors->toArray()) > 0) {
        $mapedColor = array_map(fn($item) => ["id" => $item->id, "nombre" => $item->color], $colors->toArray());
        array_push($result, array('title' => 'Colores', 'items' => $mapedColor));
      }

      return $result;
    }

    public static function getProductDetail($request)
    {
      $results = DB::table("Producto AS P")
                ->select("P.id AS id", "codigo", "nombre", "descripcion", "precio", "departamento", "categoria")
                ->join("ProductoDepartamento as PD", "PD.idProducto", "P.id")
                ->join("Departamento AS D", "D.id", "PD.idDepartamento")
                ->join("ProductoCategoria AS PC", "PC.idProducto", "P.id")
                ->join("Categoria AS C", "C.id", "PC.idCategoria");


      if (isset($request->search)) {
        $results = $results->leftJoin("ProductoTag AS PT", "PT.idProducto", "P.id")
          ->leftJoin("Tag AS T", "T.id", "PT.idTag")
          ->where("nombre", "LIKE", "%$request->search%")
          ->orWhere("tag", "LIKE", "%$request->search%");
      }

      if (isset($request->d)) {
        $results = $results->where("D.id", $request->d);
      }

      if (isset($request->c)) {
        $results = $results->where("C.id", $request->c);
      }

      $results = $results->where('P.visible', true)->get();

      if (count($results->toArray()) < 0) {
        return [];
      }

      foreach ($results as $key => $res) {
        $image = DB::table("Imagen AS I")->select("image")
          ->join("ProductoImagen AS PI", "PI.idImagen", "I.id")
          ->where("PI.idProducto", $res->id)
          ->get()->toArray();
        $res->images = array_map(fn($img) => $img->image, $image);

        $res->colors = DB::table("Color AS C")->select("C.color", "C.hex")
          ->join("ProductoColor AS PC", "PC.idColor", "C.id")
          ->where("PC.idProducto", $res->id)
          ->get();
      }

      return $results;
    }
}
