<?php

namespace App\Services;

use App\Exceptions\InvalidRequestException;
use App\Jobs\SendTemplateMessage;
use App\Models\AppointRecord;
use App\Models\CleanTeeth;
use App\Models\Customer;
use App\Models\TeethCompany;
use App\Models\WechatUser;
use App\Utils\ErrorCode;
use App\Utils\GeoHash;
use App\Utils\Wechat\AppointmentMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AppointService
{

    public function appoint($input, $user, $type = 'clean_teeth')
    {
        if ($type == 'clean_teeth') $input['time'] = make_time($input['time']);

        if (AppointRecord::checkExpired($input['time'])) {
            throw new InvalidRequestException(ErrorCode::APPOINT_EXPIRE_TIP['message']);
        }

        $data = [];
        if ($type == 'clean_teeth') $data = $this->makeCleanTeethData($input, $user);
        if ($type == 'film') $data = $this->makeFilmData($input, $user);
        $item = AppointRecord::query()->create($data);
        SendTemplateMessage::dispatch($item->user, new AppointmentMessage(), $item, 'appoint', config('miniWechat.message.appoint'));
    }

    public function queryPrice($input, $user)
    {
        $input['user_id'] = $user->id;
        $input['appoint_addr'] = (TeethCompany::companyInfo($input['company_id']))->company_name;
        $input['sale_user_id'] = Customer::userAndSale($user->id, $input['company_id']) ?: (TeethCompany::companyInfo($input['company_id']))->user_id;//老带新
        $input['type'] = AppointRecord::TYPE_INDEX['QUERY_PRICE'];//1洗牙 2拍片
        $input['appoint_status'] = AppointRecord::STATUS['SUCCESS'];//1洗牙 2拍片
        $input['obj_name'] = AppointRecord::OBJ_NAME_INDEX[AppointRecord::TYPE_INDEX['QUERY_PRICE']] . ':' . $input['obj_name'];//1洗牙 2拍片 3价格查询
        $item = AppointRecord::query()->create($input);

        SendTemplateMessage::dispatch($item->user, new AppointmentMessage(), $item, 'appoint', config('miniWechat.message.appoint'));
    }

    public function makeCleanTeethData($input, $user)
    {
        if (AppointRecord::checkAppointed($input['company_id'], $user->id)) {
            throw new InvalidRequestException(ErrorCode::APPOINT_TIP['message']);
        }
        $input['user_id'] = $user->id;
        $input['appoint_date_at'] = $input['time'];
        $input['appoint_addr'] = (TeethCompany::companyInfo($input['company_id']))->company_name;
        $input['appoint_date'] = $input['clean_tooth_date'];
        $input['sale_user_id'] = Customer::userAndSale($user->id, $input['company_id']) ?: (TeethCompany::companyInfo($input['company_id']))->user_id;//老带新
        $input['type'] = AppointRecord::TYPE_INDEX['CLEAN_TEETH'];//1洗牙 2拍片
        $input['appoint_status'] = AppointRecord::STATUS['AWAIT'];//1洗牙 2拍片
        $input['obj_name'] = AppointRecord::OBJ_NAME_INDEX[AppointRecord::TYPE_INDEX['CLEAN_TEETH']];//1洗牙 2拍片
        return $input;
    }

    public function makeFilmData($input, $user)
    {
        if (AppointRecord::isExceedDay($input, AppointRecord::MAX_DAY)) {
            throw new InvalidRequestException('预约时间不能超过' . AppointRecord::MAX_DAY . '天');
        }
        $input['user_id'] = $user->id;
        $input['appoint_date_at'] = Carbon::parse($input['time']);
        $input['appoint_addr'] = (TeethCompany::companyInfo($input['company_id']))->company_name;
        $input['appoint_status'] = AppointRecord::STATUS['SUCCESS'];//1洗牙 2拍片
        $input['sale_user_id'] = Customer::userAndSale($user->id, $input['company_id']) ?: (TeethCompany::companyInfo($input['company_id']))->user_id;//老带新
        $input['type'] = AppointRecord::TYPE_INDEX['PAI_PIAN'];//1洗牙 2拍片
        $input['obj_name'] = AppointRecord::OBJ_NAME_INDEX[AppointRecord::TYPE_INDEX['PAI_PIAN']];//1洗牙 2拍片
        return $input;
    }

    public function update($record_id, $type)
    {
        AppointRecord::query()->find($record_id)->update(['appoint_status' => $type]);
        if (in_array($type, ['cancel', 'failed'])) {
            $this->returnRecord($record_id);
        }
    }

    public function can($type, $user, $teethCompany_id)
    {
        if (in_array($type, ['success', 'expired', 'arrived', 'failed'])) {
            //只允许管理员或者是业务员操作
            if (!(WechatUser::isSale($user, $teethCompany_id) || WechatUser::isAdmin($user->id, $teethCompany_id))) {
                throw new InvalidRequestException('你的权限不够');
            }
        }
    }

    public function returnRecord($record_id)
    {
        $record = AppointRecord::query()->find($record_id);
        //如果不是洗牙记录
        if ($record->type != AppointRecord::TYPE_INDEX['CLEAN_TEETH']) return;
        Log::info('退还洗牙位置' . __LINE__);
        $clean_data = CleanTeeth::query()->where('company_id', $record->company_id)->where('clean_tooth_date', $record->appoint_date)->first();
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
