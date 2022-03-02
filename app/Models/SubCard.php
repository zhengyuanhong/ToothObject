<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCard extends Model
{
    use HasFactory;
    protected $table = 'sub_card';

    protected $fillable = ['card_id','phone'];

    public function parentCard()
    {
        return $this->belongsTo(DentalCard::class, 'card_id', 'id');
    }
}
