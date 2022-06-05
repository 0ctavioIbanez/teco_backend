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

      return $thumb;
    }
}
