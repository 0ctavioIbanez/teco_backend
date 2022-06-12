<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Admin
Route::post("/register", "AdminController@register");
Route::post("/test", "DepartamentoController@test");

// Departamentos
Route::group(["prefix" => "/departamento"], function() {
  Route::post("/create", "DepartamentoController@create");
  Route::post("/update", "DepartamentoController@update");
  Route::post("/removeImage", "DepartamentoController@removeImage");
  Route::post("/remove", "DepartamentoController@remove");
  Route::get("/get/{id?}", "DepartamentoController@get");
});

// Categorias
Route::group(["prefix" => "/categoria"], function() {
  Route::post("/create", "CategoriaController@create");
  Route::post("/update", "CategoriaController@update");
  Route::get("/get/{id?}", "CategoriaController@get");
  Route::post("/deleteAll", "CategoriaController@deleteAll");
  Route::post("/removeImage", "CategoriaController@removeImage");
});
