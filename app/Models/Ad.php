<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;
    protected $table = 'ad';

    public static function getAd()
    {
        if(empty($res = self::query()->where('active', 1)->limit(4)->get())){
            return [];
        }
        return $res->toArray();
    }

    public static function getRandomAd()
    {
        $query = self::query()->where('active', 1);
        $sum = $query->sum('pv');
        $ads = $query->get();

        $id = self::get_rand($ads, $sum);
        return self::query()->find($id)->toArray();
    }

    /**
     * @param $ads
     * @param $proSum 精度值
     * @return int|string
     */
    public static function get_rand($ads, $proSum)
    {
        $result = '';
        //概率数组循环
        foreach ($ads as $key => $val) {
            $randNum = mt_rand(1, $proSum);
            //如果中奖率大于等于随机数则取出
            if ($randNum <= $val->pv) {
                $result = $val->id;
                break;
            } else {
                $proSum -= $val->pv;
            }
        }
        unset ($ads);
        return $result;
    }
}
