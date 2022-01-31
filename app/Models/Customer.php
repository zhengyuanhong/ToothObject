<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customer';

    protected $fillable = ['user_id', 'phone', 'name'];

    public static function search($key){
       $customer =  Customer::query()->where('phone',$key)->get();
       return $customer->toArray();
    }

    public static function create($data)
    {
        //不存在就创建
        if (!self::query()->where('phone', $data['phone'])->exists()) {
            self::query()->create([
                'user_id' => (TeethCompany::company())->user_id,
                'phone' => $data['phone'],
                'name' => $data['name'],
            ]);
        }
    }

    public function getCreatedAtAttribute($key)
    {
        return Carbon::parse($key)->format('Y-m-d H:i:s');
    }
}
