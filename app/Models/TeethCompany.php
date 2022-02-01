<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Self_;

class TeethCompany extends Model
{
    use HasFactory;

    protected $table = 'teeth_company';

    protected $fillable = ['phone', 'slogan', 'user_id', 'address', 'card_name', 'company_name', 'lat', 'lon','index_head_image'];

    static public function companyInfo()
    {
        if (empty($res = self::query()->first())) {
            $res = self::query()->create(
                config('miniWechat.company')
            );
        }
    return $res;
    }

    static public function oneCompany(){
        $res = self::companyInfo();
        return [
            'id' => $res->id,
            'indicatorDots' => true,
            'index_head_image'=>$res->index_head_image,
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
