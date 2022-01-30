<?php

namespace App\Models;

use App\Events\MakeAppointMentEvent;
use App\Exceptions\InvalidRequestException;
use App\Utils\ErrorCode;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Self_;

class AppointRecord extends Model
{
    use HasFactory;

    const TYPE_INDEX = [
        'PAI_PIAN' => 1,
        'CLEAN_TEETH' => 0
    ];
    const OBJ_NAME_INDEX = [
        0 => '洗牙',
        1 => '拍片'
    ];

    const IS_CANCEL = [
        'ARRIVED' => 2,
        'YES' => 1,
        'NO' => 0
    ];

    const MAX_DAY = 31;

    protected $table = 'appoint_record';

    protected $fillable = ['user_id', 'appoint_date_at', 'type', 'obj_name', 'is_cancel', 'appoint_date', 'appoint_addr'];

    public function user(){
        return $this->belongsTo(WechatUser::class,'user_id','id');
    }

    static public function checkAppointed()
    {
        //检查三个月内是否洗牙
        $res = self::query()
            ->where('type', self::TYPE_INDEX['CLEAN_TEETH'])
            ->where('is_cancel', self::IS_CANCEL['NO'])
            ->whereBetween('appoint_date_at', [Carbon::now()->subMonths(3), Carbon::now()->addMonths(1)])
            ->exists();
        return $res;
    }

    static public function isExceedDay($data, $max)
    {
        $current_date = Carbon::parse($data['time']);
        $num = Carbon::now()->diffInDays($current_date, true);

        //预约时间不能超过一个月
        if ($num >= $max) return true;
        else return false;
    }

    static public function checkExpired($date)
    {
        return Carbon::now()->gt($date);
    }
}
