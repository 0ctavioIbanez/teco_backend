<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Talla;

class TallaController extends Controller
{
    public function get($id='')
    {
      return response(
        Talla::get()
      );
    }

    public function create(Request $request) {
      $request->validate([
        'talla' => 'required|unique:Talla|max:255',
      ]);
      return response( Talla::nueva($request) );
    }
}
