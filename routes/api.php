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
  Route::post("/create", "DepartamentoController@create");
  Route::post("/update", "DepartamentoController@update");
  Route::post("/removeImage", "DepartamentoController@removeImage");
  Route::post("/remove", "DepartamentoController@remove");
  Route::get("/get/{id?}", "DepartamentoController@get");
})->middleware("auth:api");

// Categorias
Route::group(["prefix" => "/categoria"], function() {
  Route::post("/create", "CategoriaController@create");
  Route::post("/update", "CategoriaController@update");
  Route::get("/get/{id?}", "CategoriaController@get");
  Route::post("/deleteAll", "CategoriaController@deleteAll");
  Route::post("/removeImage", "CategoriaController@removeImage");
  Route::get("/departamentos", "CategoriaController@getCategoriaDepartamento");
})->middleware("auth:api");

// Colores
Route::group(["prefix" => "/color"], function() {
  Route::get("/get", "ColorController@get");
})->middleware("auth:api");

// Tallas
Route::group(["prefix" => "/talla"], function() {
  Route::get("/get", "TallaController@get");
})->middleware("auth:api");

// Tags
Route::group(["prefix" => "/tags"], function() {
  Route::get("/get", "TagsController@get");
})->middleware("auth:api");

// Codigos
Route::group(["prefix" => "/code"], function() {
  Route::get("/generate", "CodeController@generate");
})->middleware("auth:api");

// Producto
Route::group(["prefix" => "/producto"], function() {
  Route::post("/create", "ProductoController@create");
  Route::get("/get/{id?}", "ProductoController@get");
  Route::post("/search", "ProductoController@search");
  Route::get("/search-product/{term?}", "ProductoController@searchProduct");
  Route::post("/update-color", "ProductoController@updateColor");
  Route::post("/create-color", "ProductoController@createColor");
  Route::post("/delete-color", "ProductoController@deleteColor");
  Route::post("/create-modelo", "ProductoController@createModelo");
  Route::post("/update-modelo", "ModeloController@update");
  Route::post("/delete-modelo", "ModeloController@delete");
  Route::post("/delete-image", "ProductoController@deleteImage");
  Route::post("/create-image", "ProductoController@creteImage");
  Route::post("/delete-modelo-image", "ProductoController@deleteModeloImage");
  Route::post("/create-tag", "ProductoController@createTag");
  Route::post("/delete-tag", "ProductoController@deleteTag");
  Route::post("/update", "ProductoController@update");
})->middleware("auth:api");

// Modelos
Route::group(["prefix" => "/modelos"], function() {
  Route::get("/get/{idProducto}", "ModeloController@get");
})->middleware("auth:api");

// Banners
Route::group(["prefix" => "/banners"], function() {
  Route::get("/get", "BannerController@get");
  Route::post("/upload", "BannerController@upload");
  Route::post("/erase", "BannerController@erase");
})->middleware("auth:api");

// Cards
Route::group(["prefix" => "/cards"], function() {
  Route::get("/get", "CardController@get");
  Route::post("/create", "CardController@create");
  Route::post("/remove", "CardController@remove");
  Route::post("/update", "CardController@update");
})->middleware("auth:api");

// Bodega
Route::group(["prefix" => "/bodega"], function() {
  Route::get("/get/{id?}", "BodegaController@get");
  Route::post("/create", "BodegaController@create");
  Route::get("/getCeldas/{id?}", "CeldaController@get");
  Route::get("/getCeldaProducts/{id}", "CeldaController@getProducts");
  Route::post("/createCelda", "CeldaController@create");
  Route::post("/addCeldaItem", "CeldaController@addCeldaItem");
  Route::post("/moveCelda", "CeldaController@move");
})->middleware("auth:api");

// ====================== Public
Route::get("scaffolding", "TiendaController@getScaffolding");
Route::get("store", "ProductoController@getTienda");
Route::get("search", "ProductoController@searchTienda");
Route::get("get-producto/{id}", "TiendaController@getProducto");
