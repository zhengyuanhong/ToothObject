<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class CleanTeeth extends Model
{
    use HasFactory;

    protected $table = 'clean_teeth';
    protected $casts = [
        'appoint_content' => 'array'
    ];

    protected $fillable = ['clean_tooth_date', 'appoint_content'];
}
