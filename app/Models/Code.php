<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Code extends Model
{
    use HasFactory;

    public static function generate()
    {
      $generated = null;
      $exists = null;
      $secuence = 5;

      do {
        $timer_start = microtime(true);
        $id = strrev(uniqid("C"));
        $generated = mb_strtoupper( str_split($id, $secuence)[0] );
        $exists = DB::table("Codigo")->where("codigo", $generated)->first();
        $timer_end = microtime(true);

        if (($timer_end - $timer_start) >= 1.5) {
          $secuence++;
        }

      } while ($exists);

      return $generated;
    }
}
