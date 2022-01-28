<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Models\DentalCard;
use App\Services\AppointService;
use App\Services\CleanTeethService;
use App\Services\WechatUserService;
use App\Utils\ErrorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CleanTeethController extends Controller
{
    protected $cleanTeethService = null;
    protected $wechatUserService = null;

    public function __construct(CleanTeethService $cleanTeethService, WechatUserService $wechatUserService)
    {
        $this->cleanTeethService = $cleanTeethService;
        $this->wechatUserService = $wechatUserService;
    }

    function getCleanTeethData(Request $request)
    {
        $date_arr = $this->cleanTeethService->getDateTime();
        $date = $request->get('date', $date_arr['current_date']);
        //如果没有就创建
        $teeth_data = $this->cleanTeethService->createData($date);
        //获取日期
        $date_arr['current_date'] = $date;
        $data['date_arr'] = $date_arr;
        //获取洗牙数据
        $data['clean_tooth_arr'] = $teeth_data->toArray();
        //获取预约数据
        $data['appoint_info'] = $this->wechatUserService->record($request->user('api')->id, $date);
        return $this->reponseJson(ErrorCode::SUCCESS, $data);
    }

    public function getUserRecord(Request $request)
    {
        $date = $request->get('date');
        if (empty($date)) throw new InvalidRequestException('网络延迟');

        $data['appoint_info'] = $this->wechatUserService->record($request->user('api')->id, $date);
        return $this->reponseJson(ErrorCode::SUCCESS, $data);
    }

    function updateCleanTeethData(Request $request, AppointService $appointService)
    {
        $input = $request->all();
        $validate = Validator::make($input, [
            'clean_tooth_date' => 'required',
            'appoint_content' => 'required',
            'time' => ''
        ]);
        if ($validate->fails()) {
            Log::info('App\Controller\Api\CleanTeethController@updateTeethData__' . __LINE__, ErrorCode::NO_PARAM);
            throw new InvalidRequestException('网络延迟');
        }
        //如果没有具体时间则没有预约
        if (!empty($input['time'])) {
            //是否领取看牙卡
            if (!DentalCard::cardExits(auth('api')->user()->id)) {
                throw new InvalidRequestException('no_card');
            }
            $appointService->appoint($input, auth('api')->user()->id);
        }
        //更新数据
        $this->cleanTeethService->updateData($input);
        return $this->reponseJson(ErrorCode::SUCCESS);
    }
}
