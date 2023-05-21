<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Bodega extends Model
{
    use HasFactory;

    public static function create($request)
    {
        return DB::table("Bodega")->insert([
            "bodega" => $request->bodega,
            "descripcion" => $request->descripcion
        ]);
    }

    public static function get($request)
    {
        $banner = DB::table("Bodega");

        if ($request->id) {
            return $banner->where("id", $request->id)->first();
        }
        return $banner->get();
    }
}
