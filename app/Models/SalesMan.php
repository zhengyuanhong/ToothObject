<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesMan extends Model
{
    use HasFactory;

    protected $table = 'salesman';

    public function getQrCodeAttribute($value)
    {
        return env('APP_URL').$value;
    }
}
