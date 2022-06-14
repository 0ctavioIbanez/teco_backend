<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Color;

class ColorController extends Controller
{
    public function get($id='')
    {
      return response(
        Color::get($id)
      );
    }
}
