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
}
