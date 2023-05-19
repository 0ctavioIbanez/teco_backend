<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Imagen;

class Banner extends Model
{
    use HasFactory;

    public static function get() {
        $url = url('');
        $url = str_replace("public", "storage", $url);
        $paths = DB::table("Banner")->select("url")->get()->map(fn($item) => "$url/app/public/$item->url");
        return $paths;
    }

    public static function upload($request) {
        $url = Imagen::insert($request, "banners");
        return DB::table("Banner")->insertGetId([
            "url" => $url,
        ]);
    }
}
