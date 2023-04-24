<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Color extends Model
{
    use HasFactory;

    public static function get($id='')
    {
      return DB::table("Color")->get();
    }

    public static function deleteColor($request)
    {
      try {
        // DB::table("Color")->where('id', $request->idColor)->delete();
        DB::table("ProductoColor")->where('idColor', $request->idColor)->delete();
        return ["status" => "ok", "message" => "Color eliminado correctamente"];
      } catch (\Exception $e) {
        return ["status" => "bad", "error" => $e];
      }
    }
}
