<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tags extends Model
{
    use HasFactory;

    public static function get($id='')
    {
      return DB::table("Tag")->get();
    }

    public static function createProducto($request)
    {
      foreach ($request->tags as $key => $tag) {
        $idTag = DB::table("Tag")->insertGetId(["tag" => $tag]);
        DB::table("ProductoTag")->insert(["idProducto" => $request->id, "idTag" => $idTag]);
      }
    }

    public static function remove($request)
    {
      DB::table("Tag")->where("id", $request->id)->delete();
      DB::table("ProductoTag")->where("idTag", $request->id)->delete();
    }
}
