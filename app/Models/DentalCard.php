<?php

namespace App\Models;

use App\Exceptions\InvalidRequestException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DentalCard extends Model
{
    use HasFactory;
    protected $table = 'dental_card';

    const IS_RECEIVE = 1;

    protected $fillable = [
        'number', 'phone', 'check_number', 'integral', 'is_receive', 'user_id', 'card_name', 'company_id'
    ];

    static function drawCard($user_id, $data)
    {
        //为代码健壮，在确定一次
        if (!self::query()->where('user_id', $user_id)->where('company_id', $data['company_id'])->exists()) {
            self::makeCard($user_id, $data['company_id']);
        }

        $card = self::query()->where('user_id', $user_id)->where('company_id', $data['company_id'])->first();
        $card->phone = $data['phone'];
        $card->expired_at = Carbon::now()->addYears(2)->format('Y-m-d');
        $card->name = (TeethCompany::companyInfo($data['company_id']))->card_name;
        $card->is_receive = self::IS_RECEIVE;
        $card->company_id = $data['company_id'];
        $card->save();
    }

    static public function cardExits($user_id, $data)
    {
        $exits = self::query()
            ->where('company_id', $data['company_id'])
            ->where('user_id', $user_id)->exists();

        if (!$exits) {
            self::makeCard($user_id, $data['company_id']);
            return false;
        }
        return $exits;
    }

    static public function isDrawCard($user_id, $data)
    {
        return self::query()
            ->where('company_id', $data['company_id'])
            ->where('user_id', $user_id)
            ->where('is_receive', self::IS_RECEIVE)->exists();
    }

    static public function makeCard($user_id, $company_id = null)
    {
        $card = new self();
        $card->user_id = $user_id;
        if ($company_id) {
            $card->company_id = $company_id;
        }
        $card->save();
    }

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
        static::creating(function ($model) {
            if (!$model->number) {
                $model->number = static::getCardNumber();
                if (!$model) {
                    return false;
                }
            }
        });
    }

    static function getCardNumber()
    {
        do {
            $number = 'NO.' . time() . random_int(1111, 99999);
        } while (self::query()->where('number', $number)->exists());
        return $number;
    }

    public function scopeCompanyAndUser($query, $company_id, $user_id)
    {
        return $query->where('company_id', $company_id)->where('user_id', $user_id);
    }

    public function user()
    {
        return $this->belongsTo(WechatUser::class, 'user_id', 'id');
    }

    static public function getCardInfo($company_id, $user_id)
    {
        if(!$res = self::query()->with('user')
            ->where('company_id', $company_id)
            ->where('user_id', $user_id)
            ->where('is_receive',self::IS_RECEIVE)
            ->first()){
           return false;
        }
        return $res;
    }

    public function subCard()
    {
        return $this->hasMany(SubCard::class, 'card_id', 'id');
    }
}
