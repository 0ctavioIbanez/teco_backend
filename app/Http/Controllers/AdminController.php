<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{

  public function register(Request $request, User $user)
  {
    $request->validate([
      'email' => 'email|required|unique:users',
      'password' => 'required'
    ]);

    User::create($request);
    $accessToken = $user->createToken('authToken')->accessToken;
    return response()->json([
      "token" => $accessToken
    ]);
  }

  public function login(Request $request)
  {
    $credentials = $request->validate([
      'email' => 'email|required',
      'password' => 'required'
    ]);

    if (!auth()->attempt($credentials)) {
      return response()->json(["status" => "BAD", "message" => "user not found"], 401);
    }

    $accessToken = auth()->user()->createToken('authToken')->accessToken;

    return response()->json(["token" => $accessToken, "user" => auth()->user()]);
  }

  public function checkAuth(Request $request) {
    $credentials = $request->validate([
      'email' => 'email|required',
      'password' => 'required'
    ]);
    // return response(auth()->attempt($credentials));
  }
}
