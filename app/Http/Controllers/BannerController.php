<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;

class BannerController extends Controller
{
    public function get() {
        return response(Banner::get());
    }

    public function upload(Request $request) {
        if (Banner::upload($request) > 0) {
            return response(["message" => "ok"]);
        }
        return response(["message" => "Algo salió mal"], 500);
    }

    public function erase(Request $request) {
        return response(Banner::erase($request));
    }
}
