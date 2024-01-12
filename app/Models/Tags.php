<?php

namespace App\Models;

use Hamcrest\Type\IsString;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use LDAP\Result;

class Tags extends Model
{
    use HasFactory;

    public static function get($id='')
    {
      return DB::table("Tag")->get();
    }

    public static function createProducto($request)
    {
      $existentTags = [];

      foreach ($request->tags as $key => $tag) {
        $existentTagId = self::validateTag($tag);
        
        if ($existentTagId) {
          DB::table("ProductoTag")->insert(["idProducto" => $request->productId, "idTag" => $existentTagId]);
        } else {
          $idTag = DB::table("Tag")->insertGetId(["tag" => strtolower($tag)]);
          DB::table("ProductoTag")->insert(["idProducto" => $request->productId, "idTag" => $idTag]);
        }
      }

      return ["message" => "Creado correctamente", "errorTags" => $existentTags];
    }

    private static function validateTag($tagname) {
      $result = DB::table('Tag')
        ->where("tag", strtolower($tagname))
        ->first()?->id;
      return $result > 0 ? $result : false;
    }

    public static function remove($request)
    {
      DB::table("Tag")->where("id", $request->id)->delete();
      DB::table("ProductoTag")->where("idTag", $request->id)->delete();
    }

    public static function getTagProduct($request) {
      return DB::table('ProductoTag as PT')
        ->select("PT.id as idPT", "tag")
        ->join("Tag as T", "T.id", "PT.idTag")
        ->where("PT.idProducto", $request->idProduct)
        ->get();
    }

    public static function desasociateProduct($request) {
      DB::table("ProductoTag")
        ->where("id", $request->idProductTag)
        ->delete();
      return ["message" => "Desasociado correctamente"];
    }
}
