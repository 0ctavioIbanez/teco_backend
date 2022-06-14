<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Code;

class CodeController extends Controller
{
    public function generate()
    {
      return response(["code" => Code::generate()]);
    }
}
