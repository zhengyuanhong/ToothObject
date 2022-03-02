<?php

namespace App\Services;

use App\Models\AppointRecord;
use App\Models\DentalCard;
use App\Models\WechatUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Created by PhpStorm.
 * User: zheng
 * Date: 2022/1/24
 * Time: 21:58
 */
class WechatUserService
{
    public function record($user_id, $date, $company_id)
    {
        $res = AppointRecord::query()
            ->where('company_id', $company_id)
            ->where('user_id', $user_id)
            ->where('type', AppointRecord::TYPE_INDEX['CLEAN_TEETH'])
            ->where('appoint_date', $date)
            ->whereIn('appoint_status', [AppointRecord::STATUS['AWAIT'],AppointRecord::STATUS['SUCCESS']])
            ->first();
        return !empty($res) ? $res->toArray() : false;
    }

    public function saveUser($user_id, $data)
    {
        $user = WechatUser::query()->find($user_id);
        $user->avatar = $data['avatar'];
        $user->role = WechatUser::ROLE['USER'];
        $user->name = $data['name'];
        $user->gender = $data['gender'];
//        $user->company_id = $data['company_id'];
        $user->save();
    }

    public function createUser($openid)
    {
        $user = WechatUser::query()->where('openid', $openid)->first();
        if (empty($user)) {
            $user = new WechatUser();
            $user->openid = $openid;
            $user->role = 'user';
            $user->name = make_name();
            $user->save();
            Log::info('创建user数据');
            $user->token = $user->createToken($openid)->plainTextToken;
            $user->save();

            //创建看牙卡
//            DentalCard::makeCard($user->id);
        }
        if (!$user->token) {
            $user->token = $user->createToken($openid)->plainTextToken;
        }
        $user->save();
        $data = $user->toArray();
        return $data;
    }

    public function appointNotify($company_id)
    {
        $res = auth('api')->user()->appoint_record()
            ->where('company_id', $company_id)
            ->where('appoint_status', AppointRecord::STATUS['SUCCESS'])
            ->get();

        $notify = [];
        foreach ($res as $val) {
            $dt = Carbon::parse($val->appoint_date_at);
            $week = num_to_text($dt->dayOfWeek);
            $time = $dt->format('m月d日');
            $hour = $dt->hour;

            $item['id'] = $val->id;
            $item['content'] = sprintf('您成功预约了%s(周%s)%s%s点的%s', $time, $week, getStrTime($hour), $hour, $val->obj_name);
            $notify[] = $item;
        }
        return $notify;
    }
}