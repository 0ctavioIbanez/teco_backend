<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Admin
Route::post("/register", "AdminController@register");
Route::post("/login", "AdminController@login");
Route::get("/check-auth", "AdminController@checkAuth")->middleware("auth:api");

// Departamentos
Route::group(["prefix" => "/departamento"], function() {
  Route::post("/create", "DepartamentoController@create")->middleware("auth:api");
  Route::post("/update", "DepartamentoController@update")->middleware("auth:api");
  Route::post("/removeImage", "DepartamentoController@removeImage")->middleware("auth:api");
  Route::post("/remove", "DepartamentoController@remove")->middleware("auth:api");
  Route::get("/get/{id?}", "DepartamentoController@get")->middleware("auth:api");
});

// Categorias
Route::group(["prefix" => "/categoria"], function() {
  Route::post("/create", "CategoriaController@create")->middleware("auth:api");
  Route::post("/update", "CategoriaController@update")->middleware("auth:api");
  Route::get("/get/{id?}", "CategoriaController@get")->middleware("auth:api");
  Route::post("/deleteAll", "CategoriaController@deleteAll")->middleware("auth:api");
  Route::post("/removeImage", "CategoriaController@removeImage")->middleware("auth:api");
  Route::get("/departamentos", "CategoriaController@getCategoriaDepartamento")->middleware("auth:api");
});

// Colores
Route::group(["prefix" => "/color"], function() {
  Route::get("/get", "ColorController@get")->middleware("auth:api");
});

// Tallas
Route::group(["prefix" => "/talla"], function() {
  Route::get("/get", "TallaController@get")->middleware("auth:api");
  Route::post("/create", "TallaController@create")->middleware("auth:api");
});

// Tags
Route::group(["prefix" => "/tags"], function() {
  Route::get("/get", "TagsController@get")->middleware("auth:api");
  Route::get("/get-tag-product/{idProduct}", "TagsController@getTagProduct")->middleware("auth:api");
  Route::post("/create", "TagsController@create")->middleware("auth:api");
  Route::delete("/delete-tag-product/{idProductTag}", "TagsController@desasociateProduct")->middleware("auth:api");
});

// Codigos
Route::group(["prefix" => "/code"], function() {
  Route::get("/generate", "CodeController@generate")->middleware("auth:api");
});

// Producto
Route::group(["prefix" => "/producto"], function() {
  Route::post("/create", "ProductoController@create")->middleware("auth:api");
  Route::get("/get/{id?}", "ProductoController@get")->middleware("auth:api");
  Route::get("/get-images/{productId}", "ProductoController@getImages")->middleware("auth:api");
  Route::post("/search", "ProductoController@search")->middleware("auth:api");
  Route::get("/search-product/{term?}", "ProductoController@searchProduct")->middleware("auth:api");
  Route::post("/update-color", "ProductoController@updateColor")->middleware("auth:api");
  // Route::post("/create-color", "ProductoController@createColor")->middleware("auth:api");
  Route::post("/delete-color", "ProductoController@deleteColor")->middleware("auth:api");
  Route::post("/create-modelo", "ProductoController@createModelo")->middleware("auth:api");
  Route::post("/update-modelo", "ModeloController@update")->middleware("auth:api");
  Route::post("/delete-modelo", "ModeloController@delete")->middleware("auth:api");
  Route::post("/delete-image", "ProductoController@deleteImage")->middleware("auth:api");
  Route::post("/create-image", "ProductoController@creteImage")->middleware("auth:api");
  Route::post("/delete-modelo-image", "ProductoController@deleteModeloImage")->middleware("auth:api");
  Route::post("/create-tag", "ProductoController@createTag")->middleware("auth:api");
  Route::post("/delete-tag", "ProductoController@deleteTag")->middleware("auth:api");
  Route::post("/update", "ProductoController@update")->middleware("auth:api");
  
  Route::post("/add-extra-codes", "ProductoController@addExtraCodes")->middleware("auth:api");
  Route::get("/get-extra-codes/{productId}", "ProductoController@getExtraCodes")->middleware("auth:api");
  Route::delete("/remove-extra-code/{codeId}", "ProductoController@deleteExtraCode")->middleware("auth:api");
});

// Modelos
Route::group(["prefix" => "/modelos"], function() {
  Route::get("/get/{idProducto}", "ModeloController@get")->middleware("auth:api");
  Route::post("/upload-image", "ModeloController@uploadImage")->middleware("auth:api");
  Route::post("/delete-image/{idModeloImagen}/{idImage}", "ModeloController@deleteImage")->middleware("auth:api");
  Route::post("/create-color", "ModeloController@createColor")->middleware("auth:api");
  Route::post("/delete-color/{idModelColor}", "ModeloController@deleteColor")->middleware("auth:api");
});

// Banners
Route::group(["prefix" => "/banners"], function() {
  Route::get("/get", "BannerController@get")->middleware("auth:api");
  Route::post("/upload", "BannerController@upload")->middleware("auth:api");
  Route::post("/erase", "BannerController@erase")->middleware("auth:api");
});

// Cards
Route::group(["prefix" => "/cards"], function() {
  Route::get("/get", "CardController@get")->middleware("auth:api");
  Route::post("/create", "CardController@create")->middleware("auth:api");
  Route::post("/remove", "CardController@remove")->middleware("auth:api");
  Route::post("/update", "CardController@update")->middleware("auth:api");
});

// Bodega
Route::group(["prefix" => "/bodega"], function() {
  Route::get("/get/{id?}", "BodegaController@get")->middleware("auth:api");
  Route::post("/create", "BodegaController@create")->middleware("auth:api");
  Route::get("/getCeldas/{id?}", "CeldaController@get")->middleware("auth:api");
  Route::get("/getCeldaProducts/{id}", "CeldaController@getProducts")->middleware("auth:api");
  Route::post("/createCelda", "CeldaController@create")->middleware("auth:api");
  Route::post("/addCeldaItem", "CeldaController@addCeldaItem")->middleware("auth:api");
  Route::post("/moveCelda", "CeldaController@move")->middleware("auth:api");
  Route::delete("/delete-model/{idModelCelda}", "CeldaController@deleteModel")->middleware("auth:api");
});

// ====================== Public
Route::get("scaffolding", "TiendaController@getScaffolding");
Route::get("store", "ProductoController@getTienda");
Route::get("search", "ProductoController@searchTienda");
Route::get("get-producto/{id}", "TiendaController@getProducto");
