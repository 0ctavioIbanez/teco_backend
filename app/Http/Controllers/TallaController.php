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
}
