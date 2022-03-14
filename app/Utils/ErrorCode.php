<?php

namespace App\Utils;
class ErrorCode
{
    const SUCCESS = ['code' => 200, 'message' => 'success'];
    const GET_WECHAT_OPENID_ERR = ['code' => 201, 'message' => '获取信息错误'];
    const UN_AUTH = ['code' => 401, 'message' => '未登录'];
    const NO_PARAM = ['code' => 201, 'message' => '缺少参数'];
    const APPOINT_TIP = ['code' => 201, 'message' => '建议3-6个月洗一次牙，您近期预约过，不能再预约'];
    const APPOINT_EXPIRE_TIP = ['code' => 201, 'message' => '这个时间已经过了，不能预约'];
    const NO_CARD = ['code' => 201, 'message' => '未领取看牙卡'];
    const PHONE_FAIL = ['code' => 201, 'message' => '获取手机号码失败'];
}

