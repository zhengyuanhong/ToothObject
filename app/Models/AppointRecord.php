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
        'CLEAN_TEETH' => 0,
        'QUERY_PRICE' => 2
    ];
    const OBJ_NAME_INDEX = [
        0 => '洗牙',
        1 => '拍片',
        2 => '咨询价格',
    ];

    const IS_CANCEL = [
        'ARRIVED' => 2,
        'YES' => 1,
        'NO' => 0
    ];

    const STATUS = [
        'ARRIVED' => 'arrived', //已到院
        'FAILED' => 'failed', //预约失败
        'SUCCESS' => 'success', //预约成功
        'AWAIT' => 'await', //等待
        'CANCEL' => 'cancel', //预约失败
        'EXPIRED' => 'expired' //已过期
    ];

    const MAX_DAY = 31;

    protected $table = 'appoint_record';

    protected $fillable = ['user_id', 'sale_user_id', 'cost', 'appoint_date_at', 'appoint_status', 'type', 'obj_name', 'is_cancel', 'appoint_date', 'appoint_addr', 'company_id'];

    public function user()
    {
        return $this->belongsTo(WechatUser::class, 'user_id', 'id');
    }

    static public function checkAppointed($company_id, $user_id)
    {
        //检查三个月内是否洗牙
        $res = self::query()
            ->where('type', self::TYPE_INDEX['CLEAN_TEETH'])
            ->where('company_id', $company_id)
            ->where('user_id', $user_id)
            ->where('appoint_status', self::STATUS['ARRIVED'])
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

    public function scopeCompanyAndUser($query, $company_id, $user_id)
    {
        return $query->where('company_id', $company_id)->where('user_id', $user_id);
    }

    static public function achievement($user, $company_id)
    {
        TeethCompany::isAdminOrSale($user, $company_id);

        return self::query()
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()])
            ->where('appoint_status', '<>', AppointRecord::STATUS['CANCEL'])
            ->sum('cost');
    }
}
