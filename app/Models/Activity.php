<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    protected $table = 'activity';
    protected $casts = [
        'content' => 'array'
    ];

    const STATUS = [
        'ready' => 1,
        'starting' => 2,
        'end' => 0
    ];

    protected $fillable = ['name','geo_code','images','content','address','phone','lon','lat','start_time','end_time','company_id','user_id', 'status'];

    static public function defaultData()
    {
        return [
            'menu' => [
                'pai_pian' => [
                    'text'=>'报名拍片',
                    'is_show'=>true,
                ],
                'clean_teeth' => [
                    'text'=>'预约洗牙',
                    'is_show'=>true,
                ],
                'price_jisuan' => [
                    'text'=>'咨询价格',
                    'is_show'=>true,
                ],
            ],
            'note' => ''
        ];
    }

    static public function createAct()
    {
      return  self::query()->create(
            [
                'content' => self::defaultData(),
                'name' => '看牙卡报名活动',
                'company_id' => 6,
                'user_id' => 9
            ]
        );
    }
}
