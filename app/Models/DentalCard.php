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
        'number', 'phone', 'check_number', 'integral', 'is_receive', 'user_id'
    ];

    static function drawCard($user_id, $data)
    {
        $card = self::query()->where('user_id', $user_id)->first();
        if (empty($card)) $card = self::makeCard($user_id);

        $card->phone = $data['phone'];
        $card->number = make_number($data['phone']);
        $card->expired_at = Carbon::now()->addYears(2)->format('Y-m-d');
        $card->is_receive = self::IS_RECEIVE;
        $card->save();
    }

    static public function cardExits($user_id)
    {
        return self::query()->where('user_id', $user_id)->where('is_receive',self::IS_RECEIVE)->exists();
    }

    static public function makeCard($user_id)
    {
        $card = new self();
        $card->user_id = $user_id;
        $card->name = (TeethCompany::company())->card_name;
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
}
