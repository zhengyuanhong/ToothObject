<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppointRecordResource;
use App\Models\AppointRecord;
use App\Models\Customer;
use App\Services\AppointService;
use App\Utils\ErrorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AppointController extends Controller
{
    protected $appointService = null;

    public function __construct(AppointService $appointService)
    {
        $this->appointService = $appointService;
    }

    public function updateAppoint(Request $request)
    {
        $record_id = $request->get('record_id');
        if (empty($record_id)) throw new InvalidRequestException('网络延迟');

        $type = $request->get('type','');

        //取消
        if(empty($type)) $this->appointService->update($record_id);
            //到达医院
        if($type == 'arrived') $this->appointService->update($record_id,AppointRecord::IS_CANCEL['ARRIVED']);

        //还原洗牙预约数量
        $this->appointService->returnRecord($record_id);
        return $this->reponseJson(ErrorCode::SUCCESS);
    }

    //标记已经到院
    public function arrived(Request $request)
    {
        $record_id = $request->get('record_id');
        if (empty($record_id)) throw new InvalidRequestException('网络延迟');

        //取消预约
        //还原洗牙预约数量
        $this->appointService->returnRecord($record_id);
        return $this->reponseJson(ErrorCode::SUCCESS);
    }

    public function appointFilm(Request $request)
    {
        $input = $request->all();
        $validate = Validator::make($input, [
            'name' => 'required',
            'phone' => 'required',
            'time' => 'required'
        ]);
        if ($validate->fails()) {
            Log::info('App\Controller\Api\AppointController@appointFilm' . __LINE__, ErrorCode::NO_PARAM);
            throw new InvalidRequestException('请填写完整的信息');
        }

        $this->appointService->appoint($input,auth('api')->user()->id, 'film');
        //新建客户
        Customer::create($input);
        return $this->reponseJson(ErrorCode::SUCCESS);
    }

    public function records()
    {
        $records = AppointRecord::query()
            ->where('user_id', auth('api')->user()->id)
            ->orderBy('is_cancel', 'ASC')
            ->orderBy('appoint_date_at', 'DESC')
            ->paginate(20);

        return AppointRecordResource::collection($records);

    }
}
