<?php

namespace App\Services;

use App\Models\AppointRecord;
use App\Utils\Wechat\Message;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class MessageService
{
    public function sendMessage($user,Message $message, AppointRecord $appointRecord, $type, $templateId)
    {
        $message->setToUser($user->openid);
        $date = $this->formatTime($appointRecord);
        if ($type == 'schedule') {
            $message->setTemplateId($templateId);
            $tip = sprintf('您预约了%s点的%s,请准时到达',$date['hour'],$appointRecord->obj_name);
            $note = '提前15分钟到达';
            $message->setData($appointRecord->obj_name, $appointRecord->appoint_date_at,$appointRecord->appoint_addr,$tip,$note);
//            public function setData($obj_name, $appoint_date_at,$appoint_addr,$appoint_tip,$note)
        }
        if ($type == 'appoint') {
            $message->setTemplateId($templateId);
            $note = sprintf('%s预约成功',$appointRecord->obj_name);
            $message->setData($user->name, $appointRecord->appoint_date_at, $appointRecord->appoint_addr, $note);
        }

        $message->setPage('/pages/index/index');

        $data = $message->getData();
        Log::info('模板消息内容：',$data);
        $app = app('easyWechat');
        $res = $app->subscribe_message->send($data);
        Log::info('返回内容：',$res);

        $user->message()->create([
            'type' => $type
        ]);
    }

    public function formatTime(AppointRecord $appointRecord)
    {
        $dt = Carbon::parse($appointRecord->appoint_date_at);
        $week = num_to_text($dt->dayOfWeek);
        $time = $dt->format('m月d日');
        $hour = $dt->hour;
        return ['week'=>$week, 'time'=>$time, 'hour'=>$hour];
    }
}
