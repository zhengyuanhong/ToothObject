<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Self_;

class TeethCompany extends Model
{
    use HasFactory;

    protected $table = 'teeth_company';

    protected $fillable = ['phone', 'slogan', 'user_id', 'address', 'card_name', 'company_name', 'lat', 'lon'];

    static public function oneCompany()
    {
        if (empty($res = self::query()->first())) {
            $res = self::query()->create(
                config('miniWechat.company')
            );
        }

        return [
            'id' => $res->id,
            'indicatorDots' => true,
            'phone' => $res->phone,
            'slogan' => $res->slogan,
            'user_id' => $res->user_id,
            'address' => $res->address,
            'card_name' => $res->card_name,
            'name' => $res->company_name,
            'latitude' => floatval($res->lat),
            'longitude' => floatval($res->lon),
            'markers' => [
                [
                    'id' => $res->id,
                    'latitude' => floatval($res->lat),
                    'longitude' => floatval($res->lon),
                    'title' => $res->company_name
                ]
            ]
        ];
    }
}
