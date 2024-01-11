<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Celda extends Model
{
    use HasFactory;

    public static function get($request) {
        $celda = DB::table("BodegaCelda as bc")->join("Celda as c", "c.id", "bc.idCelda");
        if ($request->id) {
            return $celda->where('bc.idBodega', $request->id)->get();
        }
        return $celda->get();
    }

    public static function create($request) {
        $id = DB::table("Celda")->insertGetId([
            "celda" => $request->celda,
            "descripcion" => $request->descripcion
        ]);
        return DB::table("BodegaCelda")->insert([
            "idBodega" => $request->idAlmacen,
            "idCelda" => $id
        ]);
    }

    public static function getProducts($request) {
        $productos = DB::table("ProductoCelda as pc")->where("idCelda", $request->id)
            ->join("Producto as p", "p.id", "pc.idProducto")
            ->join("Modelos as m", "m.idProducto", "p.id")
            ->leftJoin("ProductoTalla as pt", "pt.idProducto", "p.id")
            ->leftJoin("Talla as t", "t.id", "pt.idTalla")
            ->select("nombre", "codigo", "cantidad", "p.id as idProducto", "t.talla", "m.id as idModelo", "pc.id as idCelda")
            ->groupBy("p.id")
            ->get();

        foreach ($productos as $key => $producto) {
            $producto->colors = DB::table("ProductoColor as prodC")
                ->join("Color as c", "c.id", "prodC.idColor")
                ->select("color", "hex", "c.id")
                ->where("prodC.idProducto", $producto->idProducto)->get();
        }

        return $productos;
    }

    public static function addCeldaItem($request) {
        DB::table("ProductoCelda")->insert([
            "idCelda" => $request->idCelda,
            "idProducto" => $request->idProducto,
            "idModelo" => $request->idModelo,
            "cantidad" => $request->cantidad,
        ]);
        return ["status" => "ok", "message" => "Producto agregado correctamente"];
    }

    public static function validateCeldaItem($request) {
        return DB::table("ProductoCelda")
            ->where("idModelo", $request->idModelo)
            ->where("idCelda", $request->idCelda)
            ->where("idProducto", $request->idProducto)
            ->get()->count() > 0 ? true : false;
    }

    public static function move($request) {
        return DB::table("ProductoCelda")
            ->where("id", $request->celdaOrigin)
            ->update(["idCelda" => $request->celdaToMove]);
    }

    public static function deleteModel($request) {
        DB::table('ProductoCelda')->where("id", $request->idModelCelda)->delete();
        return ["message" => "Ubicación eliminada correctamente"];
    }
}
