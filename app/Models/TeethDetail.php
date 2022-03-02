<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeethDetail extends Model
{
    use HasFactory;

    protected $table = 'teeth_detail';

    public static function detail($type){
        return self::query()->where('type',$type)->first()->toArray();
    }
}
