<?php

namespace App\Services;

use App\Exceptions\InvalidRequestException;
use App\Jobs\SendTemplateMessage;
use App\Models\AppointRecord;
use App\Models\CleanTeeth;
use App\Models\TeethCompany;
use App\Utils\ErrorCode;
use App\Utils\Wechat\AppointmentMessage;
use Illuminate\Support\Carbon;

class AppointService
{

    public function appoint($input, $user_id, $type = 'clean_teeth')
    {
        if ($type == 'clean_teeth') $input['time'] = make_time($input['time']);

        if (AppointRecord::checkExpired($input['time'])) {
            throw new InvalidRequestException(ErrorCode::APPOINT_EXPIRE_TIP['message']);
        }

        $data = [];
        if ($type == 'clean_teeth') $data = $this->makeCleanTeethData($input, $user_id);
        if ($type == 'film') $data = $this->makeFilmData($input, $user_id);
        $item = AppointRecord::query()->create($data);
        SendTemplateMessage::dispatch($item->user,new AppointmentMessage(), $item, 'appoint', config('miniWechat.message.appoint'));
    }

    public function makeCleanTeethData($input, $user_id)
    {
        if (AppointRecord::checkAppointed()) {
            throw new InvalidRequestException(ErrorCode::APPOINT_TIP['message']);
        }
        $input['user_id'] = $user_id;
        $input['appoint_date_at'] = $input['time'];
        $input['appoint_addr'] = (TeethCompany::companyInfo())->company_name;
        $input['appoint_date'] = $input['clean_tooth_date'];
        $input['type'] = AppointRecord::TYPE_INDEX['CLEAN_TEETH'];//1洗牙 2拍片
        $input['obj_name'] = AppointRecord::OBJ_NAME_INDEX[AppointRecord::TYPE_INDEX['CLEAN_TEETH']];//1洗牙 2拍片
        return $input;
    }

    public function makeFilmData($input, $user_id)
    {
        if (AppointRecord::isExceedDay($input, AppointRecord::MAX_DAY)) {
            throw new InvalidRequestException('预约时间不能超过' . AppointRecord::MAX_DAY . '天');
        }
        $input['user_id'] = $user_id;
        $input['appoint_date_at'] = Carbon::parse($input['time']);
        $input['appoint_addr'] = (TeethCompany::companyInfo())->company_name;
        $input['type'] = AppointRecord::TYPE_INDEX['PAI_PIAN'];//1洗牙 2拍片
        $input['obj_name'] = AppointRecord::OBJ_NAME_INDEX[AppointRecord::TYPE_INDEX['PAI_PIAN']];//1洗牙 2拍片
        return $input;
    }

    public function update($record_id, $val = AppointRecord::IS_CANCEL['YES'])
    {
        AppointRecord::query()->find($record_id)->update(['is_cancel' => $val]);
    }

    public function returnRecord($record_id)
    {
        $record = AppointRecord::query()->find($record_id);
        //如果不是洗牙记录
        if ($record->type != AppointRecord::TYPE_INDEX['CLEAN_TEETH']) return;

        $clean_data = CleanTeeth::query()->where('clean_tooth_date', $record->appoint_date)->first();
        //时间格式化
        $time = Carbon::parse($record->appoint_date_at)->format('H:i');
        $appoint_content = collect($clean_data->appoint_content)->map(function ($val) use ($time) {
            if ($val['time'] == $time) {
                $val['head'] = $val['head'] + 1;
            }
            return $val;
        });
        $clean_data->appoint_content = $appoint_content;
        $clean_data->save();
    }
}
