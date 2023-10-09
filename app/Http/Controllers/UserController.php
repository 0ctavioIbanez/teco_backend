<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function create(Request $request) : void {
        $isValid = $request->validate([
            "email" => "required|unique:users|email"
        ]);
    }
}
