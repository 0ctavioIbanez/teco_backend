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
}
