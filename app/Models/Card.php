<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Card extends Model
{
    use HasFactory;

    public static function create($request)
    {
        DB::table("Card")->insert([
            "titulo" => $request->titulo,
            "icon" => $request->icon,
            "descripcion" => $request->descripcion
        ]);
        return ["message" => "Tarjeta creada correctamente"];
    }

    public static function updateCard($request)
    {
        DB::table("Card")
            ->where("id", $request->id)
            ->update([
                "titulo" => $request->titulo,
                "icon" => $request->icon,
                "descripcion" => $request->descripcion
            ]);
        return ["message" => "Tarjeta actualizada correctamente"];
    }

    public static function getCards()
    {
        return DB::table("Card")->get();
    }

    public static function remove($id)
    {
        DB::table("Card")->where("id", $id)->delete();
        return ["message" => "Eliminado correctamente"];
    }
}
