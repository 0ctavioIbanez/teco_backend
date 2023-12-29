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
        $paths = DB::table("Banner")->select("url", "id")->get()->map(fn($item) => [
            "banner" => "$url/app/public/$item->url",
            "id" => $item->id
        ]);
        return $paths;
    }

    public static function upload($request) {
        $url = Imagen::insert($request, "banners");
        return DB::table("Banner")->insertGetId([
            "url" => $url,
        ]);
    }

    public static function erase($request) {
        $banner = DB::table("Banner")->where("id", $request->id);
        $path = $banner->first()->url;
        $banner->delete();
        $stat = Imagen::erase($path);
        if ($stat) {
            return ["message" => "Banner eliminado correctamente"];
        }
        return ["message" => "Algo salió mal"];
    }
}
