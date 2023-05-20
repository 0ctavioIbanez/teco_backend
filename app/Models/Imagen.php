<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Intervention\Image\Facades\Image as Image;

class Imagen extends Model
{
    use HasFactory;

    private $encoding = "data:image/png;base64, ";

    public static function upload($image)
    {
      if (!$image) {
        return null;
      }
      return DB::table("Imagen")->insertGetId([
        "image" => "data:image/png;base64, $image"
      ]);
    }

    public static function insert($image, $folder, $name = "") {
      if (!$image) {
        return;
      }

      $url = $folder."/".time().$image->name;
      Storage::disk('public')->put($url, base64_decode($image->data));
      return $url;
    }

    public static function erase($path) {
      Storage::disk('public')->delete($path);
    }

    public static function thumb($base64)
    {
      $image = explode(',', $base64)[1];
      $imageName = "tmp" . '_' . time() . '.jpg';
      $path = Storage::disk('public')->path('/');

      $input = Storage::put($path.$imageName, base64_decode($image), 'public');
      $saved = Storage::get($path.$imageName);
      $image = Image::make($saved);
      $image = $image->resize(300)
      ->save($path."thumb.jpg");

      $thumb = base64_encode($image);
      unlink($path."thumb.jpg");

      return "data:image/png;base64, $thumb";
    }

    public static function remove($request){
          try {
            DB::table("Imagen")->where("id", $request->idImage)->delete();
            DB::table("ProductoImagen")->where("idImagen", $request->idImage)->delete();
            DB::table("ModelosImagen")->where("idImagen", $request->idImage)->delete();
            return ["status" => "ok", "message" => "Imágen eliminada correctamente"];
          } catch (\Exception $e) {
            return ["error" => $e];
          }
    }

    public static function createProducto($request)
    {
        foreach ($request->images as $key => $image) {
          $idImage = Self::upload($image);
          if ($request->isModel) {
            DB::table("ModelosImagen")->insert(["idModelo" => $request->isModel, "idImagen" => $idImage]);
          } else {
            DB::table("ProductoImagen")->insert(["idProducto" => $request->id, "idImagen" => $idImage]);
          }
        }
        return ["message" => "Imágen subida correctamente"];
    }
}
