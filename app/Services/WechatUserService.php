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
    public function record($user_id, $date)
    {
        $res = AppointRecord::query()
            ->where('user_id', $user_id)
            ->where('type', AppointRecord::TYPE_INDEX['CLEAN_TEETH'])
            ->where('appoint_date', $date)
            ->where('is_cancel', AppointRecord::IS_CANCEL['NO'])->first();
        return !empty($res) ? $res->toArray() : false;
    }

    public function saveUser($user_id, $data)
    {
        $user = WechatUser::query()->find($user_id);
        $user->avatar = $data['avatar'];
        $user->role = WechatUser::ROLE['USER'];
        $user->name = $data['name'];
        $user->gender = $data['gender'];
        $user->save();
    }

    public function createUser($openid)
    {
        $user = WechatUser::query()->with(['card'])->where('openid', $openid)->first();
        if (empty($user)) {
            $user = new WechatUser();
            $user->openid = $openid;
            $user->role = 'user';
            $user->name = make_name();
            $user->save();
            Log::info('创建user数据');

            //创建看牙卡
            DentalCard::makeCard($user->id);
        }

        $data = $user->toArray();
        $data['token'] = $user->createToken($openid)->plainTextToken;
        return $data;
    }

    public function appointNotify()
    {
        $res = auth('api')->user()->appoint_record()->where('is_cancel', AppointRecord::IS_CANCEL['NO'])->get();
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