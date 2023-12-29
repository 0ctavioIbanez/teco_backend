<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Celda;

class CeldaController extends Controller
{
    public function get(Request $request) {
        return response(Celda::get($request));
    }

    public function create(Request $request) {
        return response(Celda::create($request));
    }

    public function getProducts(Request $request) {
        return response(Celda::getProducts($request));
    }

    public function addCeldaItem(Request $request) {
        if (Celda::validateCeldaItem($request)) {
            return response(["message" => "Este modelo ya se encuentra agregado en esta celda"], 422);
        }
        return response(Celda::addCeldaItem($request));
    }

    public function move(Request $request){
        return response(Celda::move($request));
    }
}
