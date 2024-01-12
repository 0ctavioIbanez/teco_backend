<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tags;

class TagsController extends Controller
{
    public function get($id = '')
    {
      return response(Tags::get());
    }

    public function getTagProduct(Request $request) {
      return response(Tags::getTagProduct($request));
    }

    public function create(Request $request) {
      return response(Tags::createProducto($request));
    }

    public function desasociateProduct(Request $request) {
      return response(Tags::desasociateProduct($request));
    }
}
