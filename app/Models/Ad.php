<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\False_;

class Ad extends Model
{
    use HasFactory;
    protected $table = 'ad';

    public static function getAd($company_id, $scope, $limit = 1)
    {
        $query = self::query();
        if ($company_id === 0) {
            $query->where('company_id', $company_id);
        }
        $res = $query->where('active', 1)
            ->where('scope', $scope)
            ->limit($limit)->get();

        if (empty($res)) {
            return [];
        }
        return $res->toArray();
    }

    public function scopeCompany($query, $company_id)
    {
        return $query->where('company_id', $company_id);
    }

    public static function getRandomAd($company_id)
    {
        $query = self::query()->company($company_id)->where('active', 1);
        $sum = $query->sum('pv');
        $ads = $query->get();

        $id = self::get_rand($ads, $sum);
        if (empty($res = self::query()->find($id))) {
            return [];
        }
        return $res->toArray();
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
