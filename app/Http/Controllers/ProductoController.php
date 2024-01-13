<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Modelo;
use App\Models\Color;
use App\Models\Imagen;
use App\Models\Tags;

class ProductoController extends Controller
{

  public function create(Request $request)
  {
    $exists = Producto::validate($request->codigo);

    if ($exists) {
      return response(["status" => "BAD", "message" => "El código ya ha sido usado"], 420);
    }

    $idProducto = Producto::createGeneral($request);

    return response([
      "status" => $exists,
      "message" => "Producto creado correctamente",
      "productId" => $idProducto
    ]);
  }

  public function update(Request $request) {
    return response(Producto::updateGeneral($request));
  }

  public function get($id = null)
  {
    $products = Producto::get($id);

    if ($id) {
      return response($products);
    }

    return response([
      "products" => $products,
      "filters" => Producto::getFilters($products['results']),
    ]);
  }

  public function search(Request $request)
  {
    return response(Producto::search($request));
  }

  /*
    * Returns search results for search engine
    */
  public function searchTienda(Request $request)
  {
    return response(Producto::searchTienda($request));
  }

  /*
    * Returns all products according params
    */
  public function getTienda(Request $request)
  {
    return response(Producto::getProductDetail($request));
  }

  public function updateColor(Request $request)
  {
    return response(Producto::updateColor($request));
  }


  public function createColor(Request $request)
  {
    return response(Producto::createColor($request));
  }

  public function createModelo(Request $request)
  {
    return response(Modelo::createModelo($request));
  }

  public function deleteColor(Request $request)
  {
    return response(Color::deleteColor($request));
  }

  public function deleteImage(Request $request)
  {
    return response(Imagen::remove($request));
  }

  public function creteImage(Request $request)
  {
    return response(Imagen::createProducto($request));
  }

  public function deleteModeloImage(Request $request)
  {
    return response(Modelo::deleteImage($request));
  }

  public function createTag(Request $request)
  {
    return response(Tags::createProducto($request));
  }

  public function deleteTag(Request $request)
  {
    return response(Tags::remove($request));
  }

  public function searchProduct(Request $request) {
    return response(Producto::searchProduct($request->term));
  }

  public function getImages(Request $request) {
    return response(Producto::getImages($request));
  }

  public function addExtraCodes(Request $request) {
    return response(Producto::addExtraCodes($request));
  }

  public function getExtraCodes(Request $request) {
    return response(Producto::getExtraCodes($request->productId));
  }

  public function deleteExtraCode(Request $request) {
    return response(Producto::deleteExtraCode($request->codeId));
  }
}
