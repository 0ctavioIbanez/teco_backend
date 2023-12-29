<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function create(Request $request) : void {
        $isValid = $request->validate([
            "email" => "required|unique:users|email"
        ]);
    }

    public function checkAuth() {
        return response([
            Auth::guard('api')->check()
        ]);
    }
}
