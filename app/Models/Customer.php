<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customer';

    protected $fillable = ['user_id', 'sale_user_id', 'company_id', 'name', 'phone', 'note'];

    public static function search($user, $key)
    {
        if (!$user->company()->exists()) {
            return [];
        }
        $customer = Customer::query()->where('company_id', $user->company->id)->where('phone', $key)->get();
        return $customer->toArray();
    }

    public static function create($user_id, $data)
    {
        //不存在这张卡就创建
        if (self::query()->where('company_id', $data['company_id'])->where('phone', $data['phone'])->doesntExist()) {
            self::query()->create([
                'user_id' => $user_id,
                'phone' => $data['phone'],
                'name' => $data['name'],
                'sale_user_id' => empty($data['sale_user_id']) ? (TeethCompany::companyInfo($data['company_id']))->user_id : $data['sale_user_id'],
                'company_id' => $data['company_id'],
            ]);
        }
    }

    public function getCreatedAtAttribute($key)
    {
        return Carbon::parse($key)->format('Y-m-d H:i:s');
    }

    public function scopeCompanyAndUser($query, $company_id, $user_id)
    {
        return $query->where('company_id', $company_id)->where('user_id', $user_id);
    }

    public function salesman()
    {
        return $this->belongsTo(WechatUser::class, 'sale_user_id', 'id');
    }

    static public function userAndSale($user_id, $company_id)
    {
        if (!empty($res = self::query()->where('company_id', $company_id)->where('user_id', $user_id)->first())) {
            return $res->sale_user_id;
        } else {
            return false;
        }
    }
}
