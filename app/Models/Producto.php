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
      "codigo" => $request->codigo,
      "nombre" => $request->nombre,
      "descripcion" => $request->descripcion,
      "costo" => $request->costo,
      "precio" => $request->precio,
      "visible" => $request->visible,
      "nota" => $request->nota,
    ]);

    foreach ($request->categorias as $key => $categoria) {
      DB::table("ProductoCategoria")->insert([
        "idProducto" => $id,
        "idCategoria" => $categoria['categoria']
      ]);
    }

    return $id;
  }

  public static function updateGeneral($request)
  {
    DB::table("Producto")
      ->where("id", $request->idProducto)
      ->update([
        "codigo" => $request->codigo,
        "nombre" => $request->nombre,
        "descripcion" => $request->descripcion,
        "costo" => $request->costo,
        "precio" => $request->precio,
        "visible" => $request->visible,
        "nota" => $request->nota,
      ]);

    DB::table("ProductoCategoria")->where("idProducto", $request->idProducto)->delete();

    foreach ($request->categorias as $key => $categoria) {
      DB::table("ProductoCategoria")->insert([
        "idProducto" => $request->idProducto,
        "idCategoria" => $categoria['categoria']
      ]);
    }
    return ["message" => "Producto actualizado correctamente"];
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

  public static function get($id, $itemsPerPage = 12)
  {
    $producto = DB::table("Producto AS p")->select("*", "p.id AS id");

    if (!$id) {
      $results = $producto->get();

      foreach ($results as $key => $result) {
        $modelos = DB::table("Modelos AS m")->select("stock")->where("m.idProducto", $result->id)->paginate($itemsPerPage);

        $stock = 0;
        foreach ($modelos as $key => $modelo) {
          $stock += $modelo->stock;
        }
        $result->stock = $stock;
      }

      // Pagination
      $totalItems = $results->count();
      $totalPages = floor($totalItems / $itemsPerPage);
      $currentPage = 0;

      return [
        "results" => $results,
        "pagination" => [
          "total" => $totalItems,
          "pages" => $totalPages,
          "currentPage" => $currentPage
        ]
      ];
    }

    $producto = $producto->where('id', $id)->first();
    $producto->departamentos = DB::table("ProductoDepartamento AS pd")->select("d.*", "i.image")->join('Departamento AS d', 'd.id', 'pd.idDepartamento')
      ->leftJoin("Imagen as i", "i.id", "d.idImagenMain")->where('pd.idProducto', $id)->get();

    $producto->categorias = DB::table("ProductoCategoria as pc")
      ->select("c.*", "i.image", "pc.id as idPC")
      ->join("Categoria as c", 'c.id', 'pc.idCategoria')
      ->leftJoin('Imagen as i', "i.id", "c.idImagenMain")
      ->where('pc.idProducto', $id)
      ->get();

    $producto->tags = DB::table("ProductoTag as pt")->select("t.*")->join("Tag as t", 't.id', 'pt.idTag')->where('pt.idProducto', $id)->get();
    $producto->tallas = DB::table("ProductoTalla as pt")->select("t.*")->join("Talla as t", "t.id", "pt.idTalla")->where('pt.idProducto', $id)->get();
    $producto->colores = DB::table("ProductoColor as pc")->select("c.*")->join('Color as c', 'c.id', 'pc.idColor')->where('pc.idProducto', $id)->get();
    $producto->imagenes = DB::table("ProductoImagen as pi")->select("i.image", "i.id as idImage")->join("Imagen as i", "i.id", "pi.idImagen")->where('pi.idProducto', $id)->get();
    $modelos = DB::table("Modelos as m")->where("m.idProducto", $id)->get();

    foreach ($modelos as $key => $modelo) {
      $modelo->colores = DB::table("ProductoColor AS PC", "PC.idModelo", "m.id")->select("color", "hex", "idColor")->join("Color as C", "C.id", "PC.idColor")
        ->where("PC.idModelo", $modelo->id)->get();
      $modelo->images = DB::table("ModelosImagen as mi")->select("i.image", "i.id")->join("Imagen as i", "i.id", "mi.idImagen")->where("mi.idModelo", $modelo->id)->get();
      
      $modelo->almacen = DB::table("ProductoCelda as pc")
        ->join("BodegaCelda as bc", "bc.idCelda", "pc.idCelda")
        ->join("Bodega as b", "b.id", "bc.idBodega")
        ->where("pc.idProducto", $id)
        ->where("pc.idModelo", $modelo->id)
        ->select("bc.idBodega", "pc.idCelda", "pc.cantidad")
        ->first();

      if ($modelo->almacen) {
        $modelo->almacen->celdas = DB::table("Celda as c")
        ->join("BodegaCelda as bc", "bc.idCelda", "c.id")
        ->where("bc.idBodega", $modelo->almacen->idBodega)
        ->select('celda', 'idCelda')
        ->get()->map(fn($item) => ["value" => $item->idCelda, "label" => $item->celda]);
      }
    }

    $producto->modelos = $modelos;

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

    /**
     * ============= Filters starts
     */
    // Get categories Id
    $categoryProductsId = [];
    if ($request->Categorías) {
      $categoriesId = [];
      foreach ($request->Categorías as $key => $category) {
        $categoriesId[$key] = DB::table("Categoria")->select("id")->where("categoria", $category)->first()->id;
      }
      $categoryProductsId = DB::table("ProductoCategoria")->whereIn("idCategoria", $categoriesId)->select("idProducto")->distinct()->get()->map(fn ($item) => $item->idProducto);
    }

    // Get Departamentos Id
    $deptosProductsId = [];
    if ($request->Departamentos) {
      $deptosId = [];
      foreach ($request->Departamentos as $key => $depto) {
        $deptosId[$key] = DB::table("Departamento")->select("id")->where("departamento", $depto)->first()->id;
      }
      $categoriesDeptosId = DB::table("CategoriaDepartamento")->whereIn("idDepartamento", $deptosId)->select("idCategoria")->distinct()->get()->map(fn ($item) => $item->idCategoria);
      $deptosProductsId = DB::table("ProductoCategoria")->whereIn("idCategoria", $categoriesDeptosId)->select("idProducto")->distinct()->get()->map(fn ($item) => $item->idProducto);
    }

    // Get Colores id
    $colorProductsId = [];
    if ($request->Colores) {
      $colorId = [];
      foreach ($request->Colores as $key => $color) {
        $colorId[$key] = DB::table("Color")->select("id")->where("color", $color)->first()->id;
      }
      $colorProductsId = DB::table("ProductoColor")->select("idProducto")->whereIn("idColor", $colorId)->get()->map(fn ($item) => $item->idProducto);
    }

    // Get Tallas id
    $tagProductsId = [];
    if ($request->Tags) {
      $tagsId = [];
      foreach ($request->Tags as $key => $tag) {
        $tagsId[$key] = DB::table("Tag")->select("id")->where("tag", $tag)->first()->id;
      }
      $tagProductsId = DB::table("ProductoTag")->whereIn("idTag", $tagsId)->select("idProducto")->distinct()->get()->map(fn ($item) => $item->idProducto);
    }

    /**
     * ============= Filters ends
     */

    $products = DB::table("Producto AS p");

    // Keyword
    if ($request->q) {
      $products->where("p.nombre", "LIKE", "%$request->q%");
    }

    /**
     * ============= Filters apply starts
     */
    if ($categoryProductsId) {
      $products = $products->whereIn("p.id", $categoryProductsId);
    }
    if ($deptosProductsId) {
      $products = $products->whereIn("p.id", $deptosProductsId);
    }
    if ($colorProductsId) {
      $products = $products->whereIn("p.id", $colorProductsId);
    }
    if ($tagProductsId) {
      $products = $products->whereIn("p.id", $tagProductsId);
    }
    /**
     * ============= Filters apply ends
     */


    $total = $products->count();
    $offset = ($page * $limit) - $limit;
    $pages = ceil($total / $limit);
    $products = $products
      ->limit($limit)
      ->offset($offset)
      ->get();

    foreach ($products as $key => $res) {
      $exists = in_array($res->id, $temps);

      if (!$exists) {
        $response[] = $res;
        array_push($temps, $res->id);
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
    $products = DB::table("Producto AS P")->select("P.id as idProducto", "nombre", "departamento", "D.id as idDepartamento")
      ->join('ProductoDepartamento AS PD', 'PD.idProducto', 'P.id')
      ->join('Departamento AS D', 'D.id', 'PD.idDepartamento')
      ->where('visible', true)->where('nombre', 'LIKE', "%$request->kw%")->get();
    if (count($products->toArray()) > 0) {
      array_push($result, array('title' => 'Productos', 'items' => $products));
    }

    // Tags
    $tags = DB::table("Tag")->where('tag', 'LIKE', "%$request->kw%")->get();
    if (count($tags->toArray()) > 0) {
      $mapedTag = array_map(fn ($item) => ["id" => $item->id, "nombre" => $item->tag], $tags->toArray());
      array_push($result, array('title' => 'Etiquetas', 'items' => $mapedTag));
    }

    // color
    // $colors = DB::table('Color')->where('color', 'LIKE', "%$request->kw%")->get();
    // if (count($colors->toArray()) > 0) {
    //   $mapedColor = array_map(fn($item) => ["id" => $item->id, "nombre" => $item->color], $colors->toArray());
    //   array_push($result, array('title' => 'Colores', 'items' => $mapedColor));
    // }

    return $result;
  }

  public static function searchProduct($term)
  {
    return DB::table("Producto")->where("nombre", "LIKE", "%$term%")->select("nombre", "id")->get();
  }

  public static function getProductDetail($request)
  {
    // return explode("&", $request->fullUrl());
    $colors = [];
    $tags   = [];

    $results = DB::table("Producto AS P")
      ->select("P.id AS id", "codigo", "nombre", "descripcion", "precio", "categoria")
      // ->leftJoin("ProductoDepartamento as PD", "PD.idProducto", "P.id")
      // ->leftJoin("Departamento AS D", "D.id", "PD.idDepartamento")
      ->join("ProductoCategoria AS PC", "PC.idProducto", "P.id")
      ->join("Categoria AS C", "C.id", "PC.idCategoria")
      ->join("CategoriaDepartamento as CD", "CD.idCategoria", "C.id");

    // return $results->get();


    if (isset($request->search)) {
      $results = $results->leftJoin("ProductoTag AS PT", "PT.idProducto", "P.id")
        ->leftJoin("Tag AS T", "T.id", "PT.idTag")
        ->where("nombre", "LIKE", "%$request->search%")
        ->orWhere("tag", "LIKE", "%$request->search%");
    }

    if (isset($request->section)) {
      $results = $results->where("CD.idDepartamento", $request->section);
    }

    if (isset($request->category)) {
      $results = $results->where("C.id", $request->category);
    }

    $results = $results->where('P.visible', true)->get();

    if (count($results->toArray()) < 0) {
      return [];
    }

    foreach ($results as $key => $res) {
      $res->images = DB::table("Imagen AS I")->select("image", "I.id")
        ->join("ProductoImagen AS PI", "PI.idImagen", "I.id")
        ->where("PI.idProducto", $res->id)
        ->get()->toArray();

      $_tags = DB::table("Tag as T")->join("ProductoTag as PT", "PT.idtag", "T.id")->select("tag", "T.id as idTag")
        ->where("PT.idProducto", $res->id)
        ->get()->toArray();

      if (count($_tags) > 0) {
        array_push($tags, $_tags);
      }

      $_colors = DB::table("Color AS C")->select("C.color", "C.hex", "C.id as idColor")
        ->join("ProductoColor AS PC", "PC.idColor", "C.id")
        ->where("PC.idProducto", $res->id)
        ->get();
      $res->colors = $_colors;

      if (count($_colors->toArray()) > 0) {
        array_push($colors, $_colors->toArray());
      }
    }

    $colorCollection = collect($colors);
    $colors = $colorCollection->unique()->toArray();
    $colors = array_map(fn ($item) => $item[0], $colors);

    $tagCollection = collect($tags);
    $tags = $tagCollection->unique()->toArray();
    $tags = array_map(fn ($item) => $item[0], $tags);

    return [
      "results" => $results,
      "filters" => [
        "colors" => $colors,
        "tags" => $tags
      ]
    ];
  }

  public static function getPublic($id)
  {
    $product =  DB::table("Producto as P")
      ->select("id", "nombre", "descripcion", "precio")
      ->where("P.id", $id)
      ->first();

    $product->images = DB::table('Imagen as I')
      ->leftJoin("ProductoImagen as PI", "PI.idImagen", "I.id")
      ->select("I.id", "image")
      ->where("PI.idProducto", $id)
      ->get();

    $product->colors = DB::table("Color as C")
      ->join("ProductoColor as PC", "PC.idColor", "C.id")
      ->where("PC.idProducto", $id)
      ->select("color", "hex")
      ->get();

    $product->tags = DB::table("Tag as T")
      ->join("ProductoTag as PT", "T.id", "PT.idTag")
      ->select("tag", "T.id")
      ->where("PT.idProducto", $id)
      ->get();

    $product->sizes = DB::table("Talla as T")
      ->join("ProductoTalla as PT", "PT.idTalla", "T.id")
      ->select("T.id", "talla")
      ->where("PT.idProducto", $id)
      ->get();

    $modelos = DB::table("Modelos as M")->where("M.idProducto", $id)->get();

    foreach ($modelos as $key => $modelo) {
      $modelo->colors = DB::table("ProductoColor as pc")
        ->join("Color as c", "c.id", "pc.idColor")
        ->select("color", "hex", "idModelo as id")
        ->where("pc.idModelo", $modelo->id)->get();

      $modelo->images = DB::table("Imagen as i")
        ->join("ModelosImagen as mi", "i.id", "mi.idImagen")
        ->where("mi.idModelo", $modelo->id)
        ->select("image", "mi.idModelo as id")
        ->get();
    }
    // ->leftJoin("ModelosImagen as mi", "mi.idModelo", "M.id")
    // ->leftJoin("Imagen as i", "i.id", "mi.idImagen")

    

    $product->modelos = $modelos;

    return $product;
  }

  public static function updateColor($request)
  {
    return DB::table("Color")->where("id", $request->idColor)->update([
      "color" => $request->color,
      "hex" => $request->hex
    ]);
  }

  public static function createColor($request)
  {
    $idColor = DB::table("Color")->insertGetId([
      "color" => $request->color,
      "hex" => $request->hex
    ]);

    return DB::table("ProductoColor")->insert([
      "idProducto" => $request->id,
      "idModelo" => $request->idModelo,
      "idColor" => $idColor
    ]);
  }

  public static function getFilters($products)
  {
    $filters = [];
    $productsId = $products->map(fn ($product) => $product->id, $products);

    foreach ($productsId as $key => $id) {
      $categoriasId = DB::table("ProductoCategoria as PC")
        ->join("Categoria as C", "C.id", "PC.idCategoria")
        ->where("PC.idProducto", $id)
        ->select("C.id")->get()->map(fn ($item) => $item->id);

      $categorias = DB::table("ProductoCategoria as PC")->join("Categoria as C", "C.id", "PC.idCategoria")
        ->where("PC.idProducto", $id)->select("categoria")->distinct()->get()->map(fn ($item) => $item->categoria);

      $departamentos = [];

      foreach ($categoriasId as $key => $catId) {
        $departamentos[$key] = DB::table("CategoriaDepartamento AS CD")->join("Departamento AS D", "D.id", "CD.idDepartamento")
          ->select("departamento")->where("idCategoria", $catId)->first()->departamento;
      }

      $colores = DB::table("ProductoColor AS PC")->join("Color as C", "C.id", "PC.idColor")
        ->select("color")->get()->map(fn ($item) => $item->color);

      $tags = DB::table('Tag as T')->join("ProductoTag as PT", "PT.idTag", "T.id")->select("tag")
        ->distinct()->where("PT.idProducto", $id)->get()->map(fn ($item) => $item->tag);



      $filters = [
        [
          "placeholder" => "Categorías",
          "checks" => $categorias
        ],
        [
          "placeholder" => "Departamentos",
          "checks" => $departamentos
        ],
        [
          "placeholder" => "Colores",
          "checks" => $colores
        ],
        [
          "placeholder" => "Tallas",
          "checks" => []
        ],
        [
          "placeholder" => "Tags",
          "checks" => $tags
        ]
      ];
    }
    return $filters;
  }
}
